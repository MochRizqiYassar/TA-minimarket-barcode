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
        // preprocess
        $image = Image::make($filePath)
    ->greyscale()
    ->contrast(50)
    ->brightness(10)
    ->sharpen(20)
    ->resize(null, 1200, function ($constraint) {
        $constraint->aspectRatio();
    });

        $processedPath = storage_path('app/public/nota/processed.jpg');
        $image->save($processedPath);

        putenv('TESSDATA_PREFIX=C:\Program Files\Tesseract-OCR\tessdata');

        $text = (new TesseractOCR($processedPath))
    ->executable('C:\Program Files\Tesseract-OCR\tesseract.exe')
    ->lang('eng+ind')
    ->psm(6)
    ->config('tessedit_char_whitelist', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789., ')
    ->run();

        Log::info($text);

        return $text;
    }

    private function parseFlexible($text)
{
    // bersihin koma
    $text = str_replace(',', '', $text);

    $lines = preg_split('/\r\n|\r|\n/', $text);
    $results = [];

    foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // 🔥 FILTER HEADER / FOOTER
    if (
    preg_match('/(GOLDEN|JL|BEKASI|TOTAL|TUNAI|KEMBALI|PPN|SMS|INDOMARET)/i', $line)
    || preg_match('/\d+\/[A-Z]+\/\d+/i', $line)
    || str_contains($line, '/')
) continue;
    preg_match_all('/\d+/', $line, $nums);
    $numbers = $nums[0];

    if (count($numbers) < 2) continue;

    $qty = null;
    $harga = null;

    $hargaCandidates = [];

foreach ($numbers as $n) {
    $n = (int)$n;

    if ($n > 0 && $n <= 10 && !$qty) {
        $qty = $n;
    }

    if ($n >= 1000) {
        $hargaCandidates[] = $n;
    }
}

if (!empty($hargaCandidates)) {
    $harga = min($hargaCandidates); // 🔥 ambil harga satuan, bukan total
}

    if (!$qty || !$harga) continue;

    // 🔥 BERSIHKAN NAMA
    $nama = preg_replace('/[^A-Z\s]/', '', strtoupper($line)); // hanya huruf
$nama = trim(preg_replace('/\s+/', ' ', $nama));

    if (strlen($nama) < 3) continue;

        $barang = Barang::firstOrCreate(
            ['nama_barang' => $nama],
            [
                'barcode' => uniqid(),
                'id_kategori' => 1,
                'id_tipe_barang' => 1,
                'harga_beli' => $harga,
                'harga_jual' => $harga * 1.2,
            ]
        );

        $results[] = [
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'banyak' => $qty,
            'harga_satuan' => $harga,
            'id_tipe_barang' => $barang->id_tipe_barang ?? 1,
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
