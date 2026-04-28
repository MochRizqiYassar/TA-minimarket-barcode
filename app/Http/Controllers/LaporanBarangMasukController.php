<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangMasuk::with('barang', 'kulakan.supplier')
            ->where('status', 'approved');

        // FILTER TANGGAL
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal_masuk', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $laporans = $query->latest('tanggal_masuk')->get();

        // TOTAL
        $totalBarang = $laporans->sum('jumlah');

        return view('laporan.barang-masuk', compact(
            'laporans',
            'totalBarang'
        ));
    }
    public function exportPdf(Request $request)
{
    $query = BarangMasuk::with('barang', 'kulakan.supplier')
        ->where('status', 'approved');

    // FILTER TANGGAL
    if ($request->tanggal_awal && $request->tanggal_akhir) {

        $query->whereBetween('tanggal_masuk', [
            $request->tanggal_awal,
            $request->tanggal_akhir
        ]);
    }

    $laporans = $query->latest('tanggal_masuk')->get();

    $totalBarang = $laporans->sum('jumlah');

    $totalNominal = $laporans->sum(function ($item) {
        return $item->jumlah * $item->harga_beli;
    });

    $pdf = Pdf::loadView(
        'laporan.pdf-barang-masuk',
        compact(
            'laporans',
            'totalBarang',
            'totalNominal'
        )
    );

    return $pdf->download('laporan-barang-masuk.pdf');
}
}
