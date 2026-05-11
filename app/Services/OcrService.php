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
        $text = $this->process($fullPath); // ✅ STRING

        $supplier = $this->detectSupplier($text);
        $items = $this->parseFlexible($text);

        return [
            'items' => $items,
            'supplier_id' => $supplier
        ];
    }
    public function process($filePath)
{
    // 🔥 PREPROCESS IMAGE (LEBIH KUAT)
    $image = Image::make($filePath)
        ->greyscale()
        ->contrast(80)
        ->brightness(20)
        ->sharpen(30)
        ->resize(null, 1500, function ($constraint) {
            $constraint->aspectRatio();
        });

    $processedPath = storage_path('app/public/nota/processed.jpg');
    $image->save($processedPath);

    // 🔥 SET PATH TESSERACT
    putenv('TESSDATA_PREFIX=C:\Program Files\Tesseract-OCR\tessdata');

    // 🔥 OCR UPGRADE
    $text = (new TesseractOCR($processedPath))
        ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
        ->lang('eng+ind')
        ->psm(4) // 🔥 lebih cocok untuk struk kolom
        ->oem(1) // 🔥 engine LSTM (lebih akurat)
        ->config('preserve_interword_spaces', '1') // 🔥 jaga spasi
        ->config('tessedit_char_whitelist', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789., ')
        ->run();

    Log::info($text);

    return $text;
}

    private function parseFlexible($text)
{
    $text = strtoupper($text);
    $lines = preg_split('/\r\n|\r|\n/', $text);

    $results = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        if (preg_match('/(TOTAL|TUNAI|KEMBALI|PPN|JL|ALAMAT|INDOMARET|ALFAMART)/', $line)) continue;

        preg_match_all('/\d+/', $line, $nums);
        $numbers = $nums[0];

        if (count($numbers) < 2) continue;

        $total = (int) end($numbers);
        $harga = (int) prev($numbers);

        $qty = null;
        foreach ($numbers as $n) {
            if ($n <= 20) {
                $qty = (int)$n;
                break;
            }
        }

        if (!$qty || !$harga) continue;

        $nama = preg_replace('/\d+/', '', $line);
        $nama = preg_replace('/[^A-Z\s]/', '', $nama);
        $nama = trim(preg_replace('/\s+/', ' ', $nama));

        if (strlen($nama) < 3) continue;

        $results[] = [
            'nama_barang' => $nama,
            'banyak' => $qty,
            'harga_satuan' => $harga,
            'id_tipe_barang' => 1,
        ];
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
}
