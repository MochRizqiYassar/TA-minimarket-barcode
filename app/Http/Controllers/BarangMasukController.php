<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use App\Models\Kulakan;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuks = BarangMasuk::with('barang', 'kulakan.supplier')->latest('tanggal_masuk')->get();
        return view('barang-masuk.index', compact('barangMasuks'));
    }

    public function create()
    {
        $barangs  = Barang::all();
        $kulakans = Kulakan::where('status', 'approved')->get();
        return view('barang-masuk.create', compact('barangs', 'kulakans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'        => 'required|exists:barang,id_barang',
            'id_kulakan'       => 'required|exists:kulakan,id_kulakan',
            'jumlah'           => 'required|integer|min:1',
            'tanggal_masuk'    => 'required|date',
            'tanggal_expired'  => 'nullable|date|after:tanggal_masuk',
        ]);

        BarangMasuk::create($request->only(
            'id_barang', 'id_kulakan', 'jumlah', 'tanggal_masuk', 'tanggal_expired'
        ));

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load('barang', 'kulakan.supplier');
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        $barangs  = Barang::all();
        $kulakans = Kulakan::where('status', 'approved')->get();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'kulakans'));
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $request->validate([
            'id_barang'       => 'required|exists:barang,id_barang',
            'id_kulakan'      => 'required|exists:kulakan,id_kulakan',
            'jumlah'          => 'required|integer|min:1',
            'tanggal_masuk'   => 'required|date',
            'tanggal_expired' => 'nullable|date|after:tanggal_masuk',
        ]);

        $barangMasuk->update($request->only(
            'id_barang', 'id_kulakan', 'jumlah', 'tanggal_masuk', 'tanggal_expired'
        ));

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diperbarui.');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
