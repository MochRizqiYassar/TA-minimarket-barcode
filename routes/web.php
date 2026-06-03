<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KulakanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\Admin\UserController;
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'kasir') {
            return redirect()->route('kasir.dashboard');
        }
        return redirect('/login');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{id}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
    Route::delete('/admin/users/{id}/reject', [UserController::class, 'reject'])
        ->name('admin.users.reject');
    Route::resource('kulakan', KulakanController::class);
    Route::post('kulakan/{kulakan}/approve', [KulakanController::class, 'approve'])->name('kulakan.approve');
    Route::post('/kulakan/ocr', [KulakanController::class, 'ocr'])->name('kulakan.ocr');
    Route::resource('suppliers', SupplierController::class);
    Route::get('/barang/stok-realtime', [BarangController::class, 'stokRealtime'])->name('barang.stok-realtime');
    Route::resource('barang', BarangController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::post('barang-masuk/{barangMasuk}/approve', [BarangMasukController::class, 'approve'])
        ->name('barang-masuk.approve');
    Route::resource('kategoris', KategoriController::class);
    Route::resource('tipe-barang', TipeBarangController::class);
    Route::get('/barcode', [BarangController::class, 'formBarcodeManual'])->name('barcode.form');
    Route::post('/barcode', [BarangController::class, 'generateBarcodeManual'])->name('barcode.generate');

    Route::get('/laporan/barang-masuk', [App\Http\Controllers\LaporanBarangMasukController::class, 'index'])
        ->name('laporan.barang-masuk');
    Route::get('/laporan/barang-masuk/pdf', [App\Http\Controllers\LaporanBarangMasukController::class, 'exportPdf'])
        ->name('laporan.barang-masuk.pdf');
    Route::get('/laporan/penjualan', [App\Http\Controllers\LaporanPenjualanController::class, 'index'])
        ->name('laporan.penjualan');
    Route::get('/laporan/penjualan/pdf', [App\Http\Controllers\LaporanPenjualanController::class, 'exportPdf'])
        ->name('laporan.penjualan.pdf');
    Route::get('/laporan/barang-terlaris', [App\Http\Controllers\LaporanBarangTerlarisController::class, 'index'])
        ->name('laporan.barang-terlaris');

    Route::get('/laporan/barang-terlaris/pdf', [App\Http\Controllers\LaporanBarangTerlarisController::class, 'exportPdf'])
        ->name('laporan.barang-terlaris.pdf');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', function () {
        return view('kasir.dashboard');
    })->name('kasir.dashboard');
    Route::resource('penjualan', PenjualanController::class);
    Route::post('penjualan/{penjualan}/approve', [PenjualanController::class, 'approve'])
        ->name('penjualan.approve');
});

require __DIR__ . '/auth.php';
