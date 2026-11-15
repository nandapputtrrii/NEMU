<?php

use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BarangController::class, 'home'])->name('home');
Route::get('/nemu', [BarangController::class, 'home'])->name('nemu');
Route::get('/laporan', [BarangController::class, 'index'])->name('laporan.index');
Route::get('/laporan/create', [BarangController::class, 'create'])->name('laporan.create');
Route::post('/laporan', [BarangController::class, 'store'])->name('laporan.store');
Route::get('/laporan/{barang}/edit', [BarangController::class, 'edit'])->name('laporan.edit');
Route::put('/laporan/{barang}', [BarangController::class, 'update'])->name('laporan.update');
Route::delete('/laporan/{barang}', [BarangController::class, 'destroy'])->name('laporan.destroy');
Route::get('/lapor-hilang', [BarangController::class, 'createLost'])->name('laporan.lost.create');
Route::get('/lapor-temuan', [BarangController::class, 'createFound'])->name('laporan.found.create');
Route::view('/login', 'pages.login')->name('login');
