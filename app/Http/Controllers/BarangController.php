<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DetailKulakan;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;


class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori', 'tipeBarang')->paginate(10);
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
            'barcode'        => 'nullable|string|unique:barang,barcode',
            'nama_barang'    => 'required|string|max:255',
            'id_kategori'    => 'required|exists:kategori,id_kategori',
            'id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'harga_beli'     => 'required|numeric|min:0',
            'harga_jual'     => 'required|numeric|min:0',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stok_minimum_etalase' => 'required|integer|min:0',
            'stok_minimum_gudang' => 'required|integer|min:0',
        ]);

        $data = $request->except('foto');
        // AUTO GENERATE BARCODE JIKA KOSONG
        if (empty($data['barcode'])) {
            $data['barcode'] = 'BRG-' . strtoupper(Str::random(8));
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        // 🔥 CREATE BARANG BARU
        Barang::create([
            'barcode' => $data['barcode'],
            'nama_barang' => $data['nama_barang'],
            'id_kategori' => $data['id_kategori'],
            'id_tipe_barang' => $data['id_tipe_barang'],
            'stok' => 0,
            'stok_minimum_etalase' => $data['stok_minimum_etalase'],
            'stok_minimum_gudang' => $data['stok_minimum_gudang'],
            'harga_beli' => $data['harga_beli'],
            'harga_jual' => $data['harga_jual'],
            'foto' => $data['foto'] ?? null,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori', 'tipeBarang', 'barangMasuks', 'detailPenjualans');
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $details = DetailKulakan::with('barang')
            ->select('id_barang')
            ->distinct()
            ->get();

        $kategoris   = Kategori::all();
        $tipeBarangs = TipeBarang::all();

        return view('barang.edit', compact('barang', 'kategoris', 'tipeBarangs'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'barcode'        => 'required|string|   unique:barang,barcode,' . $barang->id_barang . ',id_barang',
            'nama_barang'    => 'required|string|max:255',
            'id_kategori'    => 'required|exists:kategori,id_kategori',
            'id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'harga_beli'     => 'required|numeric|min:0',
            'harga_jual'     => 'required|numeric|min:0',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stok_minimum_etalase' => 'required|integer|min:0',
            'stok_minimum_gudang' => 'required|integer|min:0',
        ]);

        $data = $request->except('foto');
        // AUTO GENERATE BARCODE JIKA KOSONG
        if (empty($data['barcode'])) {
            $data['barcode'] = 'BRG-' . strtoupper(Str::random(8));
        }

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
    public function formBarcodeManual()
    {
        return view('barcode.form');
    }
    public function generateBarcodeManual(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
            'jumlah' => 'required|integer|min:1|max:100'
        ]);

        $kode = $request->kode;
        $jumlah = $request->jumlah;

        // 🔥 WAJIB base64
        $barcode = DNS1D::getBarcodeHTML($kode, 'C128');

        $pdf = Pdf::loadView('barcode.pdf', compact('kode', 'jumlah', 'barcode'));

        return $pdf->setPaper('A4')->stream('barcode.pdf');
    }
    public function stokRealtime()
{
    return response()->json(
        Barang::select('id_barang', 'stok')->get()
    );
}
}
