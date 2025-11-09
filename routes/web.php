<?php

use App\Livewire\HalamanLaporanKas;
use App\Livewire\HalamanLaporanLabaRugi;
use App\Livewire\HalamanLaporanNeraca;
use App\Livewire\HalamanTransaksi;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/transaksi');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/transaksi', HalamanTransaksi::class)->name('transaksi');
    Route::get('/laporan/arus-kas', HalamanLaporanKas::class)->name('laporan.kas');
    Route::get('/laporan/laba-rugi', HalamanLaporanLabaRugi::class)->name('laporan.laba-rugi');
    Route::get('/laporan/neraca', HalamanLaporanNeraca::class)->name('laporan.neraca');

    Route::view('/profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
