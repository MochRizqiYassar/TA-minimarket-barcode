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
    Route::resource('kulakan', KulakanController::class);
    Route::post('kulakan/{kulakan}/approve', [KulakanController::class, 'approve'])->name('kulakan.approve');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::resource('kategoris', KategoriController::class);
    Route::resource('tipe-barang', TipeBarangController::class);
    Route::get('/barcode', [BarangController::class, 'formBarcodeManual'])->name('barcode.form');
    Route::post('/barcode', [BarangController::class, 'generateBarcodeManual'])->name('barcode.generate');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', function () {
        return view('kasir.dashboard');
    })->name('kasir.dashboard');
});

require __DIR__ . '/auth.php';
