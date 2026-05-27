<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Kulakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailKulakan;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuks = BarangMasuk::with('barang', 'kulakan.supplier')->latest('tanggal_masuk')->get();
        return view('barang-masuk.index', compact('barangMasuks'));
    }

    public function create()
    {
        $details = DetailKulakan::with('barang', 'kulakan')
            ->where('banyak', '>', 0)
            ->whereNotNull('id_barang')
            ->get()
            ->groupBy('id_barang')
            ->map(function ($group) {

                $first = $group->first();

                $barang = Barang::find($first->id_barang);

                return [
                    'id_barang'   => $first->id_barang,
                    'nama_barang' => $barang?->nama_barang ?? 'Barang sudah dihapus',
                    'stok'        => $group->sum('banyak'),
                    'foto'        => $barang?->foto,
                    'barcode'     => $barang?->barcode,
                ];
            });

        return view('barang-masuk.create', compact('details'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $items = json_decode($request->details_json, true);

            if (!$items) {
                throw new \Exception('Tidak ada barang dipilih!');
            }

            foreach ($items as $item) {

                $jumlah = (int) $item['qty'];

                if ($jumlah <= 0) continue;

                $idBarang = $item['id'];

                // Ambil detail kulakan FIFO
                $details = DetailKulakan::where('id_barang', $idBarang)
                    ->where('banyak', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($details as $detail) {

                    if ($jumlah <= 0) break;

                    $ambil = min($jumlah, $detail->banyak);

                    // SIMPAN BARANG MASUK
                    BarangMasuk::create([
                        'id_barang' => $idBarang,
                        'id_kulakan' => $detail->id_kulakan,
                        'jumlah' => $ambil,
                        'tanggal_masuk' => now(),
                        'tanggal_expired' => $item['tanggal_expired'] ?? null,
                        'nama_barang' => $detail->barang?->nama_barang ?? 'Barang lama',
                        'harga_beli' => $detail->barang?->harga_beli ?? 0,
                    ]);

                    // 🔥 LANGSUNG KURANGI STOK KULAKAN
                    $detail->decrement('banyak', $ambil);

                    // 🔥 LANGSUNG TAMBAH STOK BARANG
                    $detail->barang->increment('stok', $ambil);

                    $jumlah -= $ambil;
                }

                if ($jumlah > 0) {
                    throw new \Exception('Stok kulakan tidak mencukupi!');
                }
            }
        });

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Barang berhasil masuk ke etalase');
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load('barang', 'kulakan.supplier');
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
