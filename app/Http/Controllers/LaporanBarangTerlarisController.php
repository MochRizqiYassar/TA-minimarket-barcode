<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBarangTerlarisController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $laporans = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')

            ->select(
                'detail_penjualan.nama_barang',

                DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'),

                DB::raw('SUM(detail_penjualan.subtotal) as total_omzet'),

                DB::raw('SUM(detail_penjualan.total_laba) as total_laba')
            )

            ->where('penjualan.status', 'approved')

            ->whereMonth('penjualan.tanggal_penjualan', $bulan)

            ->whereYear('penjualan.tanggal_penjualan', $tahun)

            ->groupBy('detail_penjualan.nama_barang')

            ->orderByDesc('total_terjual')

            ->get();

        return view('laporan.barang-terlaris', compact(
            'laporans',
            'bulan',
            'tahun'
        ));
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $laporans = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')

            ->select(
                'detail_penjualan.nama_barang',

                DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'),

                DB::raw('SUM(detail_penjualan.subtotal) as total_omzet'),

                DB::raw('SUM(detail_penjualan.total_laba) as total_laba')
            )

            ->where('penjualan.status', 'approved')

            ->whereMonth('penjualan.tanggal_penjualan', $bulan)

            ->whereYear('penjualan.tanggal_penjualan', $tahun)

            ->groupBy('detail_penjualan.nama_barang')

            ->orderByDesc('total_terjual')

            ->get();

        $pdf = Pdf::loadView(
            'laporan.pdf-barang-terlaris',
            compact(
                'laporans',
                'bulan',
                'tahun'
            )
        );

        return $pdf->download('laporan-barang-terlaris.pdf');
    }
}
