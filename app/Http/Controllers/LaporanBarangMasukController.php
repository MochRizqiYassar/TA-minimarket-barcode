<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBarangMasukController extends Controller
{

    private function buildQuery(Request $request)
    {
        $query = BarangMasuk::with('barang', 'kulakan.supplier');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_masuk', [
                $request->tanggal_awal,
                $request->tanggal_akhir,
            ]);
        }

        return $query->latest('tanggal_masuk')->get();
    }

    public function index(Request $request)
    {
        $laporans    = $this->buildQuery($request);
        $totalBarang = $laporans->sum('jumlah');

        return view('laporan.barang-masuk', compact('laporans', 'totalBarang'));
    }

    public function exportPdf(Request $request)
    {
        $laporans     = $this->buildQuery($request);
        $totalBarang  = $laporans->sum('jumlah');
        $totalNominal = $laporans->sum(fn($item) => $item->jumlah * $item->harga_beli);

        $pdf = Pdf::loadView('laporan.pdf-barang-masuk', compact('laporans', 'totalBarang', 'totalNominal'));
        return $pdf->download('laporan-barang-masuk.pdf');
    }
}
