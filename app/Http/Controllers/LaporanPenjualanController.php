<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = Penjualan::with(['user', 'detailPenjualans'])
            ->where('status', 'approved');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_penjualan', [
                $request->tanggal_awal,
                $request->tanggal_akhir,
            ]);
        }

        return $query->latest('tanggal_penjualan')->get();
    }

    public function index(Request $request)
    {
        $laporans   = $this->buildQuery($request);
        $totalOmzet = $laporans->sum('total_harga');
        $totalLaba  = $laporans->sum(fn($p) => $p->detailPenjualans->sum('total_laba'));

        return view('laporan.penjualan', compact('laporans', 'totalOmzet', 'totalLaba'));
    }

    public function exportPdf(Request $request)
    {
        $laporans   = $this->buildQuery($request);
        $totalOmzet = $laporans->sum('total_harga');
        $totalLaba  = $laporans->sum(fn($p) => $p->detailPenjualans->sum('total_laba'));

        $pdf = Pdf::loadView('laporan.pdf-penjualan', compact('laporans', 'totalOmzet', 'totalLaba'));
        return $pdf->download('laporan-penjualan.pdf');
    }
}
