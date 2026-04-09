<?php

namespace App\Http\Controllers;

use App\Models\TipeBarang;
use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    public function index()
    {
        $tipeBarangs = TipeBarang::all();
        return view('tipe-barang.index', compact('tipeBarangs'));
    }

    public function create()
    {
        return view('tipe-barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
        ]);

        TipeBarang::create($request->only('nama_tipe'));

        return redirect()->route('tipe-barang.index')->with('success', 'Tipe barang berhasil ditambahkan.');
    }

    public function show(TipeBarang $tipeBarang)
    {
        $tipeBarang->load('barangs');
        return view('tipe-barang.show', compact('tipeBarang'));
    }

    public function edit(TipeBarang $tipeBarang)
    {
        return view('tipe-barang.edit', compact('tipeBarang'));
    }

    public function update(Request $request, TipeBarang $tipeBarang)
    {
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
        ]);

        $tipeBarang->update($request->only('nama_tipe'));

        return redirect()->route('tipe-barang.index')->with('success', 'Tipe barang berhasil diperbarui.');
    }

    public function destroy(TipeBarang $tipeBarang)
    {
        $tipeBarang->delete();
        return redirect()->route('tipe-barang.index')->with('success', 'Tipe barang berhasil dihapus.');
    }
}
