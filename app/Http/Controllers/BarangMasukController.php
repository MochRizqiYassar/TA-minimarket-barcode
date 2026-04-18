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
            ->whereHas('kulakan', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereNotNull('id_barang') // 🔥 penting
            ->get()
            ->groupBy('id_barang')
            ->map(function ($group) {

                $first = $group->first();

                return [
                    'id_barang'   => $first->id_barang,
                    'nama_barang' => $first->barang?->nama_barang ?? 'Barang sudah dihapus',
                    'stok'        => $group->sum('banyak'),
                ];
            });

        return view('barang-masuk.create', compact('details'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            foreach ($request->items as $item) {

                $jumlah = (int) $item['jumlah'];
                if ($jumlah <= 0) continue;

                $idBarang = $item['id_barang'];

                // 🔥 Ambil semua detail kulakan (FIFO)
                $details = DetailKulakan::where('id_barang', $idBarang)
                    ->where('banyak', '>', 0)
                    ->whereHas('kulakan', function ($q) {
                        $q->where('status', 'approved');
                    })
                    ->orderBy('created_at') // FIFO
                    ->get();

                foreach ($details as $detail) {

                    if ($jumlah <= 0) break;

                    $ambil = min($jumlah, $detail->banyak);

                    // Kurangi stok kulakan
                    $detail->decrement('banyak', $ambil);

                    // Tambah stok barang
                    $detail->barang->increment('stok', $ambil);

                    // Simpan barang masuk
                    BarangMasuk::create([
                        'id_barang' => $idBarang,
                        'id_kulakan' => $detail->id_kulakan,
                        'jumlah' => $ambil,
                        'tanggal_masuk' => now(),
                        'tanggal_expired' => $item['tanggal_expired'],
                        'nama_barang' => $detail->barang?->nama_barang ?? 'Barang lama',
                        'harga_beli' => $detail->barang?->harga_beli ?? 0,
                    ]);

                    $jumlah -= $ambil;
                }

                if ($jumlah > 0) {
                    throw new \Exception('Stok kulakan tidak mencukupi!');
                }
            }
        });

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Barang masuk berhasil');
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load('barang', 'kulakan.supplier');
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        return view('barang-masuk.edit', compact('barangMasuk'));
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $request->validate([
            'tanggal_expired' => 'nullable|date',
        ]);

        $barangMasuk->update([
            'tanggal_expired' => $request->tanggal_expired
        ]);

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Tanggal expired berhasil diperbarui');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
