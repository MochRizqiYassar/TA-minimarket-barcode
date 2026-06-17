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
            'tanggal_penjualan' => 'required|date',
            'details_json'      => 'required|json',
        ]);

        $details = json_decode($request->details_json, true);

        // [FIX #2] Validasi sebelum masuk transaksi, kembalikan response yang rapi
        if (!$details || count($details) === 0) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada barang dipilih!'], 422);
            }
            return back()->withErrors(['details_json' => 'Tidak ada barang dipilih!']);
        }

        DB::transaction(function () use ($request, $details) {
            $totalHarga = 0;

            $penjualan = Penjualan::create([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'id_user'           => Auth::id(),
                'total_harga'       => 0,
                'status'            => 'pending',
            ]);

            foreach ($details as $detail) {
                $barang = Barang::findOrFail($detail['id_barang']);

                if ($barang->stok < $detail['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi.");
                }

                $harga      = $barang->harga_jual;
                $hargaBeli  = $barang->harga_beli;
                $jumlah     = $detail['jumlah'];
                $subtotal   = $harga * $jumlah;
                $labaSatuan = $harga - $hargaBeli;
                $totalLaba  = $labaSatuan * $jumlah;

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
                    'nama_barang'               => $barang->nama_barang,
                    'harga_snapshot'            => $harga,
                ]);
            }

            $penjualan->update(['total_harga' => $totalHarga]);
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penjualan berhasil dicatat.']);
        }

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dicatat.');
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load('user', 'detailPenjualans.barang');
        return view('penjualan.show', compact('penjualan'));
    }

    public function edit(Penjualan $penjualan)
    {
        if ($penjualan->status == 'approved') {
            return back()->with('error', 'Data sudah diapprove, tidak bisa diedit');
        }

        $barangs = Barang::where('stok', '>', 0)->get();
        $penjualan->load('detailPenjualans');

        return view('penjualan.edit', compact('penjualan', 'barangs'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        if ($penjualan->status == 'approved') {
            return back()->with('error', 'Data sudah diapprove, tidak bisa diedit');
        }

        $request->validate([
            'tanggal_penjualan'   => 'required|date',
            'details'             => 'required|array|min:1',
            'details.*.id_barang' => 'required|exists:barang,id_barang',
            'details.*.jumlah'    => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $penjualan) {
            $details = $penjualan->detailPenjualans()->get();

            // [FIX #7] Kembalikan stok barang lama sebelum detail dihapus
            if ($penjualan->status === 'approved') {
                foreach ($details as $detail) {
                    $barang = Barang::find($detail->id_barang);
                    if ($barang) {
                        $barang->increment('stok', $detail->jumlah);
                    }
                }
            }

            $penjualan->detailPenjualans()->delete();

            $penjualan->update([
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'status'            => 'pending', // reset ke pending setelah edit
            ]);

            $totalHarga = 0;

            foreach ($request->details as $detail) {
                $barang = Barang::findOrFail($detail['id_barang']);

                $harga      = $barang->harga_jual;
                $hargaBeli  = $barang->harga_beli;
                $jumlah     = $detail['jumlah'];
                $subtotal   = $harga * $jumlah;
                $labaSatuan = $harga - $hargaBeli;
                $totalLaba  = $labaSatuan * $jumlah;

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
                    'nama_barang'               => $barang->nama_barang,
                    'harga_snapshot'            => $harga,
                ]);
            }

            $penjualan->update(['total_harga' => $totalHarga]);
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan)
    {
        DB::transaction(function () use ($penjualan) {
            $details = DetailPenjualan::where('id_penjualan', $penjualan->id_penjualan)->get();

            if ($penjualan->status === 'approved') {
                foreach ($details as $detail) {
                    $barang = Barang::find($detail->id_barang);
                    if ($barang) {
                        $barang->stok += $detail->jumlah;
                        $barang->save();
                    }
                }
            }

            DetailPenjualan::where('id_penjualan', $penjualan->id_penjualan)->delete();
            $penjualan->delete();
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
    }

    public function approve(Penjualan $penjualan)
    {
        if ($penjualan->status === 'approved') {
            return back()->with('error', 'Sudah diapprove');
        }

        DB::transaction(function () use ($penjualan) {
            foreach ($penjualan->detailPenjualans as $detail) {
                $barang = Barang::find($detail->id_barang);

                if (!$barang) {
                    throw new \Exception("Barang tidak ditemukan.");
                }

                if ($barang->stok < $detail->jumlah) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak cukup.");
                }

                $barang->decrement('stok', $detail->jumlah);
            }

            $penjualan->update(['status' => 'approved']);
        });

        return back()->with('success', 'Penjualan berhasil diapprove');
    }
}
