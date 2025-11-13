<?php

use App\Livewire\Admin\ActivityLogs;
use App\Livewire\Admin\BackupCenter;
use App\Livewire\Admin\ManageOperators;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\SiteDashboard;
use App\Livewire\Admin\VillageDashboard;
use App\Livewire\HalamanLaporanKas;
use App\Livewire\HalamanLaporanLabaRugi;
use App\Livewire\HalamanLaporanNeraca;
use App\Livewire\HalamanTransaksi;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/transaksi');

Route::middleware(['auth', 'verified', 'role:operator,admin_desa,admin_web'])->group(function () {
    Route::get('/transaksi', HalamanTransaksi::class)->name('transaksi');
    Route::get('/laporan/arus-kas', HalamanLaporanKas::class)->name('laporan.kas');
    Route::get('/laporan/laba-rugi', HalamanLaporanLabaRugi::class)->name('laporan.laba-rugi');
    Route::get('/laporan/neraca', HalamanLaporanNeraca::class)->name('laporan.neraca');

    Route::view('/profile', 'profile')->name('profile');
});

Route::middleware(['auth', 'verified', 'role:admin_desa'])->prefix('admin-desa')->name('admin-desa.')->group(function () {
    Route::get('/dashboard', VillageDashboard::class)->name('dashboard');
    Route::get('/operators', ManageOperators::class)->name('operators');
});

Route::middleware(['auth', 'verified', 'role:admin_web'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', SiteDashboard::class)->name('dashboard');
    Route::get('/users', ManageUsers::class)->name('users');
    Route::get('/logs', ActivityLogs::class)->name('logs');
    Route::get('/backup', BackupCenter::class)->name('backup');
});

require __DIR__.'/auth.php';
