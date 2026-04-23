<?php

namespace App\Http\Controllers;

use App\Models\Kulakan;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\TipeBarang;
use App\Models\DetailKulakan;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KulakanController extends Controller
{
    public function index()
    {
        $kulakans = Kulakan::with('supplier')->latest()->get();
        return view('kulakan.index', compact('kulakans'));
    }

    public function create()
    {
        $suppliers   = Supplier::all();
        $barangs     = Barang::with('tipeBarang')->get();
        $tipeBarangs = TipeBarang::all();
        return view('kulakan.create', compact('suppliers', 'barangs', 'tipeBarangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_supplier'       => 'required|exists:supplier,id_supplier',
            'tanggal_kulakan'   => 'required|date',
            'details'           => 'required|array|min:1',
            'details.*.id_barang'      => 'required|exists:barang,id_barang',
            'details.*.id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'details.*.banyak'         => 'required|integer|min:1',
            'details.*.harga_satuan'   => 'required|numeric|min:0',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
        ]);

        DB::transaction(function () use ($request) {
            $totalHarga = 0;

            $kulakan = Kulakan::create([
                'id_supplier'     => $request->id_supplier,
                'tanggal_kulakan' => $request->tanggal_kulakan,
                'status'          => 'pending',
                'total_harga'     => 0,
            ]);

            foreach ($request->details as $detail) {
                $subtotal    = $detail['banyak'] * $detail['harga_satuan'];
                $totalHarga += $subtotal;

                $barang = Barang::find($detail['id_barang']);

                DetailKulakan::create([
                    'id_kulakan' => $kulakan->id_kulakan,
                    'id_barang' => $detail['id_barang'],
                    'id_tipe_barang' => $detail['id_tipe_barang'],
                    'banyak' => $detail['banyak'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,

                    // 🔥 snapshot
                    'nama_barang' => $barang?->nama_barang,
                    'harga_satuan_snapshot' => $detail['harga_satuan'],
                ]);
            }

            $kulakan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('kulakan.index')->with('success', 'Kulakan berhasil ditambahkan.');
    }

    public function show(Kulakan $kulakan)
    {
        $kulakan->load('supplier', 'detailKulakans.barang', 'detailKulakans.tipeBarang', 'barangMasuks.barang');
        return view('kulakan.show', compact('kulakan'));
    }

    public function edit(Kulakan $kulakan)
    {
        if ($kulakan->status === 'approved') {
            return redirect()->route('kulakan.index')->with('error', 'Kulakan yang sudah approved tidak dapat diedit.');
        }

        $suppliers   = Supplier::all();
        $barangs     = Barang::with('tipeBarang')->get();
        $tipeBarangs = TipeBarang::all();
        $kulakan->load('detailKulakans');

        return view('kulakan.edit', compact('kulakan', 'suppliers', 'barangs', 'tipeBarangs'));
    }

    public function update(Request $request, Kulakan $kulakan)
    {
        if ($kulakan->status === 'approved') {
            return redirect()->route('kulakan.index')->with('error', 'Kulakan yang sudah approved tidak dapat diubah.');
        }

        $request->validate([
            'id_supplier'     => 'required|exists:supplier,id_supplier',
            'tanggal_kulakan' => 'required|date',
            'details'         => 'required|array|min:1',
            'details.*.id_barang'      => 'required|exists:barang,id_barang',
            'details.*.id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'details.*.banyak'         => 'required|integer|min:1',
            'details.*.harga_satuan'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $kulakan) {
            $kulakan->update([
                'id_supplier'     => $request->id_supplier,
                'tanggal_kulakan' => $request->tanggal_kulakan,
            ]);

            $kulakan->detailKulakans()->delete();

            $totalHarga = 0;

            foreach ($request->details as $detail) {
                $subtotal = $detail['banyak'] * $detail['harga_satuan'];
                $totalHarga += $subtotal;

                // 🔥 CEK / BUAT BARANG
                $barang = Barang::firstOrCreate(
                    ['nama_barang' => $detail['nama_barang']],
                    [
                        'barcode' => uniqid(),
                        'id_kategori' => 1,
                        'id_tipe_barang' => $detail['id_tipe_barang'],
                        'harga_beli' => $detail['harga_satuan'],
                        'harga_jual' => $detail['harga_satuan'] * 1.2,
                    ]
                );

                $subtotal = $detail['banyak'] * $detail['harga_satuan'];

                DetailKulakan::create([
                    'id_kulakan' => $kulakan->id_kulakan,
                    'id_barang' => $barang->id_barang,
                    'id_tipe_barang' => $detail['id_tipe_barang'],
                    'banyak' => $detail['banyak'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,

                    // 🔥 snapshot
                    'nama_barang' => $barang->nama_barang,
                    'harga_satuan_snapshot' => $detail['harga_satuan'],
                ]);
            }

            $kulakan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('kulakan.index')->with('success', 'Kulakan berhasil diperbarui.');
    }

    // Approve kulakan → otomatis buat barang_masuk & update stok
    public function approve(Kulakan $kulakan)
    {
        if ($kulakan->status === 'approved') {
            return redirect()->route('kulakan.index')->with('error', 'Kulakan sudah approved sebelumnya.');
        }

        DB::transaction(function () use ($kulakan) {
            $kulakan->update(['status' => 'approved']);
        });

        return redirect()->route('kulakan.show', $kulakan)->with('success', 'Kulakan berhasil diapprove dan stok diperbarui.');
    }

    public function destroy(Kulakan $kulakan)
    {
        if ($kulakan->status === 'approved') {
            return redirect()->route('kulakan.index')->with('error', 'Kulakan yang sudah approved tidak dapat dihapus.');
        }

        $kulakan->detailKulakans()->delete();
        $kulakan->delete();

        return redirect()->route('kulakan.index')->with('success', 'Kulakan berhasil dihapus.');
    }
}
