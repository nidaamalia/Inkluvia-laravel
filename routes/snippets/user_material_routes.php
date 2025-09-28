<?php

use App\Http\Controllers\PerpustakaanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/perpustakaan', [PerpustakaanController::class, 'index'])->name('perpustakaan.index');
    Route::post('/perpustakaan/store', [PerpustakaanController::class, 'store'])->name('perpustakaan.store');
});
