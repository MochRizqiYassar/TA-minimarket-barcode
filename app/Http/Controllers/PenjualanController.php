<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with('user')->latest('tanggal_penjualan')->get();
        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('penjualan.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_penjualan'  => 'required|date',
            'details'            => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.jumlah'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $totalHarga = 0;

            $penjualan = Penjualan::create([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'id_user'           => Auth::id(),
                'total_harga'       => 0,
            ]);

            foreach ($request->details as $detail) {
                $barang = Barang::findOrFail($detail['id_barang']);

                if ($barang->stok < $detail['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
                }

                $harga        = $barang->harga_jual;
                $hargaBeli    = $barang->harga_beli;
                $jumlah       = $detail['jumlah'];
                $subtotal     = $harga * $jumlah;
                $labaSatuan   = $harga - $hargaBeli;
                $totalLaba    = $labaSatuan * $jumlah;
                $totalHarga  += $subtotal;

                DetailPenjualan::create([
                    'id_penjualan'              => $penjualan->id_penjualan,
                    'id_barang'                 => $barang->id_barang,
                    'jumlah'                    => $jumlah,
                    'harga'                     => $harga,
                    'harga_beli_saat_transaksi' => $hargaBeli,
                    'subtotal'                  => $subtotal,
                    'laba_satuan'               => $labaSatuan,
                    'total_laba'                => $totalLaba,
                ]);

                // Kurangi stok
                $barang->decrement('stok', $jumlah);
            }

            $penjualan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dicatat.');
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load('user', 'detailPenjualans.barang');
        return view('penjualan.show', compact('penjualan'));
    }

    public function edit(Penjualan $penjualan)
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $penjualan->load('detailPenjualans');
        return view('penjualan.edit', compact('penjualan', 'barangs'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'tanggal_penjualan'   => 'required|date',
            'details'             => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.jumlah'    => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $penjualan) {
            // Kembalikan stok lama
            foreach ($penjualan->detailPenjualans as $old) {
                $old->barang->increment('stok', $old->jumlah);
            }

            $penjualan->detailPenjualans()->delete();
            $penjualan->update(['tanggal_penjualan' => $request->tanggal_penjualan]);

            $totalHarga = 0;

            foreach ($request->details as $detail) {
                $barang = Barang::findOrFail($detail['id_barang']);

                if ($barang->stok < $detail['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
                }

                $harga       = $barang->harga_jual;
                $hargaBeli   = $barang->harga_beli;
                $jumlah      = $detail['jumlah'];
                $subtotal    = $harga * $jumlah;
                $labaSatuan  = $harga - $hargaBeli;
                $totalLaba   = $labaSatuan * $jumlah;
                $totalHarga += $subtotal;

                DetailPenjualan::create([
                    'id_penjualan'              => $penjualan->id_penjualan,
                    'id_barang'                 => $barang->id_barang,
                    'jumlah'                    => $jumlah,
                    'harga'                     => $harga,
                    'harga_beli_saat_transaksi' => $hargaBeli,
                    'subtotal'                  => $subtotal,
                    'laba_satuan'               => $labaSatuan,
                    'total_laba'                => $totalLaba,
                ]);

                $barang->decrement('stok', $jumlah);
            }

            $penjualan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        DB::transaction(function () use ($penjualan) {
            // Kembalikan stok
            foreach ($penjualan->detailPenjualans as $detail) {
                $detail->barang->increment('stok', $detail->jumlah);
            }
            $penjualan->detailPenjualans()->delete();
            $penjualan->delete();
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}
