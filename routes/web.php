<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BarangController::class, 'home'])->name('home');
Route::get('/nemu', [BarangController::class, 'home'])->name('nemu');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/laporan', [BarangController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [BarangController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [BarangController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{barang}/edit', [BarangController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{barang}', [BarangController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{barang}', [BarangController::class, 'destroy'])->name('laporan.destroy');
    Route::get('/lapor-hilang', [BarangController::class, 'createLost'])->name('laporan.lost.create');
    Route::get('/lapor-temuan', [BarangController::class, 'createFound'])->name('laporan.found.create');
});
