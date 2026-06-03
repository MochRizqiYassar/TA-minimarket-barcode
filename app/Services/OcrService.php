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

        $path = $request->file('nota_image')->store('nota', 'public');
        $fullPath = storage_path('app/public/' . $path);
        $text = $this->process($fullPath);

        // DEBUG OCR TEXT
        Log::info($text);

        $supplier = $this->detectSupplier($text);
        $items = $this->parseFlexible($text);

        // DEBUG HASIL OCR
        Log::info($items);

        return [
            'items' => $items,
            'supplier_id' => $supplier
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

        $processedPath = storage_path(
            'app/public/nota/' . uniqid() . '_processed.jpg'
        );

        $image->save($processedPath);

        putenv('TESSDATA_PREFIX=C:\Program Files\Tesseract-OCR\tessdata');

        $text = (new TesseractOCR($processedPath))
            ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
            ->lang('eng+ind')
            ->psm(4)
            ->oem(1)
            ->config('preserve_interword_spaces', '1')
            ->run();

        unlink($processedPath);

        return $this->normalizeText($text);
    }

    private function normalizeText($text)
    {
        $text = strtoupper($text);

        // normalisasi newline dulu
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // hapus karakter aneh
        $text = str_replace(
            ['—', '«', '~', '*', '=', '©'],
            ' ',
            $text
        );

        // rapikan spasi TANPA menghapus newline
        $lines = explode("\n", $text);

        $cleanLines = [];

        foreach ($lines as $line) {

            $line = preg_replace('/[ \t]+/', ' ', $line);

            $cleanLines[] = trim($line);
        }

        return implode("\n", $cleanLines);
    }

    private function parseFlexible($text)
{
    $lines = preg_split('/\r\n|\r|\n/', $text);

    $results = [];

    foreach ($lines as $line) {

        $line = trim($line);

        if (empty($line)) {
            continue;
        }

        Log::info("LINE OCR: " . $line);

        // skip header/footer
        if (preg_match(
            '/TOTAL|TUNAI|KEMBALI|PPN|DPP|LAYANAN|CALL|BELANJA|KONSUMEN/i',
            $line
        )) {
            continue;
        }

        // normalisasi karakter aneh
        $line = str_replace(
    ['—', '«', '~', '*', '=', '©', ':', '§'],
    ' ',
    $line
);

        /*
        FORMAT YANG DICARI:
        NAMA QTY HARGA TOTAL

        contoh:
        NUTRI SARI JERUK 3 5500 16,500
        */

        if (preg_match(
            '/^(.*?)\s+(\d{1,3})\s+([\d\.,]+)\s+([\d\.,]+)$/',
            $line,
            $match
        )) {

            $nama = trim($match[1]);

            $qty = $this->fixNumber($match[2]);
            $harga = $this->fixNumber($match[3]);
            $total = $this->fixNumber($match[4]);

            Log::info([
                'nama' => $nama,
                'qty' => $qty,
                'harga' => $harga,
                'total' => $total,
            ]);

            // validasi
            if (
    $qty <= 0 ||
    $qty > 100 ||
    $harga <= 0 ||
    $total <= 0
) {
    continue;
}

// koreksi subtotal OCR
$expectedTotal = $qty * $harga;

// kalau selisih terlalu jauh
if (abs($total - $expectedTotal) > 5000) {

    Log::warning([
        'TOTAL OCR SALAH' => $total,
        'EXPECTED' => $expectedTotal,
    ]);

    $total = $expectedTotal;
}

            $barang = $this->findBarang($nama);

            if (!$barang) {

                Log::warning("BARANG TIDAK COCOK: " . $nama);

                continue;
            }

            $results[] = [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'banyak' => $qty,
                'harga_satuan' => $harga,
                'subtotal' => $total,
                'confidence' => $this->calculateConfidence(
                    $nama,
                    $barang->nama_barang
                ),
            ];
        }
    }

    return $results;
}

    private function detectSupplier($text)
    {
        if (!is_string($text)) return [];

        $text = strtoupper($text);

        $nama = null;

        if (str_contains($text, 'INDOMARET')) $nama = 'Indomaret';
        elseif (str_contains($text, 'ALFAMART')) $nama = 'Alfamart';
        elseif (str_contains($text, 'GOLDEN')) $nama = 'Golden Square';

        if (!$nama) return null;

        // 🔥 CEK DI DB
        $supplier = Supplier::where('nama_supplier', 'LIKE', "%$nama%")->first();

        // 🔥 KALAU BELUM ADA → BUAT
        if (!$supplier) {
            $supplier = Supplier::create([
                'nama_supplier' => $nama,
                'kontak' => '-',
                'alamat' => '-'
            ]);
        }

        // 🔥 RETURN ID (PENTING)
        return $supplier->id_supplier;
    }
    private function findBarang($ocrName)
{
    $ocrName = strtoupper(trim($ocrName));

    // normalisasi OCR umum
    $ocrName = str_replace([
        '0'
    ], [
        'O'
    ], $ocrName);

    $best = null;
    $highest = 0;

    $barangs = Barang::all();

    foreach ($barangs as $barang) {

    $dbName = strtoupper(trim($barang->nama_barang));

    similar_text($ocrName, $dbName, $percent);

    // bonus kalau mengandung kata sama
    $this->similarWords($ocrName, $dbName, $percent);

    if ($percent > $highest) {
        $highest = $percent;
        $best = $barang;
    }
}

    Log::info([
        'OCR' => $ocrName,
        'MATCH' => $best?->nama_barang,
        'CONFIDENCE' => $highest
    ]);

    if ($highest >= 40) {
        return $best;
    }

    return null;
}
private function similarWords($a, $b, &$score)
{
    $wordsA = explode(' ', $a);
    $wordsB = explode(' ', $b);

    $same = array_intersect($wordsA, $wordsB);

    $bonus = count($same) * 10;

    $score += $bonus;

    if ($score > 100) {
        $score = 100;
    }
}
    private function calculateConfidence($ocr, $db)
    {
        similar_text(
            strtoupper($ocr),
            strtoupper($db),
            $percent
        );

        return round($percent);
    }
    private function fixNumber($value)
    {
        // OCR sering salah baca
        $value = str_replace(
            ['O', 'I', 'S'],
            ['0', '1', '5'],
            $value
        );

        // hapus simbol
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int) $value;
    }
}
