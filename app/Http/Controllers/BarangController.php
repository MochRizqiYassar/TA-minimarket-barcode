<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori', 'tipeBarang')->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris   = Kategori::all();
        $tipeBarangs = TipeBarang::all();
        return view('barang.create', compact('kategoris', 'tipeBarangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode'       => 'required|string|unique:barang,barcode',
            'nama_barang'   => 'required|string|max:255',
            'id_kategori'   => 'required|exists:kategori,id_kategori',
            'id_tipe_barang'=> 'required|exists:tipe_barang,id_tipe_barang',
            'stok'          => 'required|integer|min:0',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori', 'tipeBarang', 'barangMasuks', 'detailPenjualans');
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategoris   = Kategori::all();
        $tipeBarangs = TipeBarang::all();
        return view('barang.edit', compact('barang', 'kategoris', 'tipeBarangs'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'barcode'       => 'required|string|unique:barang,barcode,' . $barang->id_barang . ',id_barang',
            'nama_barang'   => 'required|string|max:255',
            'id_kategori'   => 'required|exists:kategori,id_kategori',
            'id_tipe_barang'=> 'required|exists:tipe_barang,id_tipe_barang',
            'stok'          => 'required|integer|min:0',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
