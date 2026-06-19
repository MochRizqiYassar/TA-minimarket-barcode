<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Log;
use App\Models\Barang;
use App\Models\Supplier;

class OcrService
{
    public function handleUpload($request)
    {
        if (!$request->hasFile('nota_image')) {
            throw new \Exception('File tidak ditemukan');
        }

        $path     = $request->file('nota_image')->store('nota', 'public');
        $fullPath = storage_path('app/public/' . $path);
        $text     = $this->process($fullPath);

        Log::info('OCR TEXT: ' . $text);

        // [FIX #12] Bungkus deteksi supplier dalam try-catch agar tidak buat data yatim
        $supplierId = null;
        try {
            $supplierId = $this->detectSupplier($text);
        } catch (\Exception $e) {
            Log::warning('Deteksi supplier gagal: ' . $e->getMessage());
        }

        $items = $this->parseItems($text);

        Log::info('OCR ITEMS: ', $items);

        return [
            'items'       => $items,
            'supplier_id' => $supplierId,
        ];
    }

    public function process($filePath)
    {
        $image = Image::make($filePath)
            ->greyscale()
            ->contrast(70)
            ->brightness(15)
            ->sharpen(25)
            ->resize(null, 1800, function ($constraint) {
                $constraint->aspectRatio();
            });

        $processedPath = storage_path('app/public/nota/' . uniqid() . '_processed.jpg');
        $image->save($processedPath);

        // [FIX] Default path mengikuti OS yang sedang berjalan, bukan hardcode Linux.
        // Kalau TESSERACT_PATH / TESSDATA_PREFIX diisi di .env, nilai itu yang dipakai.
        // Kalau tidak diisi, baru jatuh ke default sesuai OS (Windows vs Linux).
        $isWindows = stripos(PHP_OS, 'WIN') === 0;

        $defaultTesseract = $isWindows
            ? 'C:/Program Files/Tesseract-OCR/tesseract.exe'
            : '/usr/bin/tesseract';

        $defaultTessdata = $isWindows
            ? 'C:/Program Files/Tesseract-OCR/tessdata'
            : '/usr/share/tesseract-ocr/4.00/tessdata';

        $tesseractPath = env('TESSERACT_PATH', $defaultTesseract);
        $tessdata      = env('TESSDATA_PREFIX', $defaultTessdata);

        putenv('TESSDATA_PREFIX=' . $tessdata);

        $ocr = (new TesseractOCR($processedPath))
            ->lang('eng+ind')
            ->psm(4)
            ->oem(1)
            ->config('preserve_interword_spaces', '1')
            ->executable($tesseractPath);

        $text = $ocr->run();

        @unlink($processedPath);

        return $this->normalizeText($text);
    }

    // [FIX #1] Rename parseFlexible → parseItems agar bisa dipanggil dari controller
    public function parseItems($text)
    {
        $lines   = preg_split('/\r\n|\r|\n/', $text);
        $results = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) continue;

            Log::info('LINE OCR: ' . $line);

            // Skip header/footer umum
            if (preg_match('/TOTAL|TUNAI|KEMBALI|PPN|DPP|LAYANAN|CALL|BELANJA|KONSUMEN/i', $line)) {
                continue;
            }

            $line = str_replace(['—', '«', '~', '*', '=', '©', ':', '§'], ' ', $line);

            // Format: NAMA QTY HARGA TOTAL
            if (preg_match('/^(.*?)\s+(\d{1,3})\s+([\d\.,]+)\s+([\d\.,]+)$/', $line, $match)) {
                $nama  = trim($match[1]);
                $qty   = $this->fixNumber($match[2]);
                $harga = $this->fixNumber($match[3]);
                $total = $this->fixNumber($match[4]);

                if ($qty <= 0 || $qty > 100 || $harga <= 0 || $total <= 0) {
                    continue;
                }

                // Koreksi subtotal yang salah baca OCR
                $expectedTotal = $qty * $harga;
                if (abs($total - $expectedTotal) > 5000) {
                    Log::warning('Total OCR dikoreksi', ['ocr' => $total, 'expected' => $expectedTotal]);
                    $total = $expectedTotal;
                }

                $barang = $this->findBarang($nama);

                if (!$barang) {
                    Log::warning('Barang tidak cocok: ' . $nama);
                    continue;
                }

                $results[] = [
                    'id_barang'   => $barang->id_barang,
                    'nama_barang' => $barang->nama_barang,
                    'banyak'      => $qty,
                    'harga_satuan'=> $harga,
                    'subtotal'    => $total,
                    'confidence'  => $this->calculateConfidence($nama, $barang->nama_barang),
                ];
            }
        }

        return $results;
    }

    private function normalizeText($text)
    {
        $text  = strtoupper($text);
        $text  = str_replace(["\r\n", "\r"], "\n", $text);
        $text  = str_replace(['—', '«', '~', '*', '=', '©'], ' ', $text);
        $lines = explode("\n", $text);

        $cleanLines = [];
        foreach ($lines as $line) {
            $cleanLines[] = trim(preg_replace('/[ \t]+/', ' ', $line));
        }

        return implode("\n", $cleanLines);
    }

    // [FIX #12] detectSupplier tidak lagi auto-create supplier di sini
    // Supplier hanya dicari, tidak dibuat otomatis (cegah data yatim)
    private function detectSupplier($text)
    {
        if (!is_string($text)) return null;

        $text = strtoupper($text);
        $nama = null;

        if (str_contains($text, 'INDOMARET'))     $nama = 'Indomaret';
        elseif (str_contains($text, 'ALFAMART'))  $nama = 'Alfamart';
        elseif (str_contains($text, 'GOLDEN'))    $nama = 'Golden Square';

        if (!$nama) return null;

        $supplier = Supplier::where('nama_supplier', 'LIKE', "%$nama%")->first();

        return $supplier?->id_supplier;
    }

    private function findBarang($ocrName)
    {
        $ocrName = strtoupper(trim($ocrName));
        $ocrName = str_replace(['0'], ['O'], $ocrName);

        $best    = null;
        $highest = 0;

        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            $dbName = strtoupper(trim($barang->nama_barang));

            similar_text($ocrName, $dbName, $percent);

            $this->similarWords($ocrName, $dbName, $percent);

            if ($percent > $highest) {
                $highest = $percent;
                $best    = $barang;
            }
        }

        Log::info(['OCR' => $ocrName, 'MATCH' => $best?->nama_barang, 'CONFIDENCE' => $highest]);

        // [FIX #10] Naikkan threshold dari 40% ke 60% untuk kurangi false match
        if ($highest >= 60) {
            return $best;
        }

        return null;
    }

    private function similarWords($a, $b, &$score)
    {
        $wordsA = explode(' ', $a);
        $wordsB = explode(' ', $b);

        $same  = array_intersect($wordsA, $wordsB);
        $bonus = count($same) * 10;
        $score += $bonus;

        if ($score > 100) $score = 100;
    }

    private function calculateConfidence($ocr, $db)
    {
        similar_text(strtoupper($ocr), strtoupper($db), $percent);
        return round($percent);
    }

    private function fixNumber($value)
    {
        $value = str_replace(['O', 'I', 'S'], ['0', '1', '5'], $value);
        $value = preg_replace('/[^0-9]/', '', $value);
        return (int) $value;
    }
}
