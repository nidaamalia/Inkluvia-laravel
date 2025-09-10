<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LembagaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes - Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/jadwal-belajar', function () {
            return view('user.jadwal-belajar');
        })->name('jadwal-belajar');
        Route::get('/request-materi', function () {
            return view('user.request-materi');
        })->name('request-materi');
        Route::get('/perpustakaan', function () {
            return view('user.perpustakaan');
        })->name('perpustakaan');
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/manajemen-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');
        Route::get('/manajemen-pengguna/create', [UserController::class, 'create'])->name('kelola-pengguna.create');
        Route::post('/manajemen-pengguna', [UserController::class, 'store'])->name('kelola-pengguna.store');
        Route::get('/manajemen-pengguna/{user}/edit', [UserController::class, 'edit'])->name('kelola-pengguna.edit');
        Route::put('/manajemen-pengguna/{user}', [UserController::class, 'update'])->name('kelola-pengguna.update');
        Route::delete('/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('kelola-pengguna.destroy');
        
        // Lembaga Management
        Route::get('/manajemen-lembaga', [LembagaController::class, 'index'])->name('manajemen-lembaga');
        Route::get('/manajemen-lembaga/create', [LembagaController::class, 'create'])->name('manajemen-lembaga.create');
        Route::post('/manajemen-lembaga', [LembagaController::class, 'store'])->name('manajemen-lembaga.store');
        Route::get('/manajemen-lembaga/{lembaga}/edit', [LembagaController::class, 'edit'])->name('manajemen-lembaga.edit');
        Route::put('/manajemen-lembaga/{lembaga}', [LembagaController::class, 'update'])->name('manajemen-lembaga.update');
        Route::delete('/manajemen-lembaga/{lembaga}', [LembagaController::class, 'destroy'])->name('manajemen-lembaga.destroy');
        
        // Other admin routes (placeholder)
        Route::get('/manajemen-perangkat', function () {
            return view('admin.manajemen-perangkat');
        })->name('kelola-perangkat');
        Route::get('/manajemen-materi', function () {
            return view('admin.manajemen-materi');
        })->name('pengaturan');
    });
});

// API routes untuk future development
Route::prefix('api')->group(function () {
    Route::get('/lembagas', function () {
        return response()->json(\App\Models\Lembaga::all());
    });
});