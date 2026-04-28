<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with([
            'user',
            'detailPenjualans'
        ])->where('status', 'approved');

        // FILTER TANGGAL
        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween('tanggal_penjualan', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $laporans = $query->latest('tanggal_penjualan')->get();

        $totalOmzet = $laporans->sum('total_harga');

        $totalLaba = $laporans->sum(function ($penjualan) {
            return $penjualan->detailPenjualans->sum('total_laba');
        });

        return view('laporan.penjualan', compact(
            'laporans',
            'totalOmzet',
            'totalLaba'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Penjualan::with([
            'user',
            'detailPenjualans'
        ])->where('status', 'approved');

        // FILTER TANGGAL
        if ($request->tanggal_awal && $request->tanggal_akhir) {

            $query->whereBetween('tanggal_penjualan', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $laporans = $query->latest('tanggal_penjualan')->get();

        $totalOmzet = $laporans->sum('total_harga');

        $totalLaba = $laporans->sum(function ($penjualan) {
            return $penjualan->detailPenjualans->sum('total_laba');
        });

        $pdf = Pdf::loadView(
            'laporan.pdf-penjualan',
            compact(
                'laporans',
                'totalOmzet',
                'totalLaba'
            )
        );

        return $pdf->download('laporan-penjualan.pdf');
    }
}
