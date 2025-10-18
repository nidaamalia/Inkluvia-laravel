<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LembagaController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserMaterialController;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/institution/register', [\App\Http\Controllers\InstitutionRegistrationController::class, 'store'])
        ->name('institution.register');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('role:admin')->get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::middleware('role:user')->get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    
    // User routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/jadwal-belajar', [JadwalController::class, 'index'])->name('jadwal-belajar');
        Route::get('/jadwal-belajar/create', [JadwalController::class, 'create'])->name('jadwal-belajar.create');
        Route::post('/jadwal-belajar', [JadwalController::class, 'store'])->name('jadwal-belajar.store');
        Route::get('/jadwal-belajar/{jadwal}/edit', [JadwalController::class, 'edit'])->name('jadwal-belajar.edit');
        Route::put('/jadwal-belajar/{jadwal}', [JadwalController::class, 'update'])->name('jadwal-belajar.update');
        Route::delete('/jadwal-belajar/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal-belajar.destroy');
        Route::get('/jadwal-belajar/{jadwal}/start', [JadwalController::class, 'startSession'])->name('jadwal-belajar.start');
        Route::post('/jadwal-belajar/{jadwal}/send', [JadwalController::class, 'sendToDevices'])->name('jadwal-belajar.send');
        Route::get('/jadwal-belajar/{jadwal}/learn', [JadwalController::class, 'learn'])->name('jadwal-belajar.learn');
        Route::post('/jadwal-belajar/{jadwal}/complete', [JadwalController::class, 'completeSession'])->name('jadwal-belajar.complete');
        Route::post('/jadwal-belajar/{jadwal}/navigate', [JadwalController::class, 'navigatePage'])->name('jadwal-belajar.navigate');
        Route::post('/jadwal-belajar/{jadwal}/material-page', [JadwalController::class, 'getMaterialPage'])->name('jadwal-belajar.material-page');
        
        // Device Text Routes
        Route::prefix('device')->name('device.')->group(function () {
            Route::post('/send-text', [\App\Http\Controllers\User\DeviceTextController::class, 'sendText'])
                 ->name('send-text');
            Route::get('/list', [\App\Http\Controllers\User\DeviceTextController::class, 'listDevices'])
                 ->name('list');
        });
                
        // Materi Saya routes
        Route::get('/materi-saya', [UserMaterialController::class, 'index'])->name('materi-saya');
        Route::get('/materi-saya/create', [UserMaterialController::class, 'create'])->name('materi-saya.create');
        Route::post('/materi-saya', [UserMaterialController::class, 'store'])->name('materi-saya.store');
        Route::get('/materi-saya/{material}/edit', [UserMaterialController::class, 'edit'])->name('materi-saya.edit');
        Route::put('/materi-saya/{material}', [UserMaterialController::class, 'update'])->name('materi-saya.update');
        Route::delete('/materi-saya/{material}', [UserMaterialController::class, 'destroy'])->name('materi-saya.destroy');
        Route::get('/materi-saya/{material}/preview', [UserMaterialController::class, 'preview'])->name('materi-saya.preview');
        Route::get('/materi-saya/{material}/download', [UserMaterialController::class, 'download'])->name('materi-saya.download');

        // NEW: Content editing routes
        Route::get('/materi-saya/{material}/edit-content', [UserMaterialController::class, 'editContent'])->name('materi-saya.edit-content');
        Route::post('/materi-saya/{material}/update-content', [UserMaterialController::class, 'updateContent'])->name('materi-saya.update-content');
        Route::get('/materi-saya/{material}/page/{pageNumber}', [UserMaterialController::class, 'getPageContent'])->name('materi-saya.get-page');
        
        // Perpustakaan routes
        Route::get('/perpustakaan', [PerpustakaanController::class, 'index'])->name('perpustakaan');
        Route::get('/materi-tersimpan', [PerpustakaanController::class, 'savedMaterials'])->name('materi-tersimpan');
        Route::post('/perpustakaan/{material}/toggle-saved', [PerpustakaanController::class, 'toggleSaved'])->name('perpustakaan.toggle-saved');
        Route::get('/perpustakaan/{material}/preview', [PerpustakaanController::class, 'preview'])->name('perpustakaan.preview');
        Route::get('/perpustakaan/{material}/preview-page', [PerpustakaanController::class, 'showPreview'])->name('perpustakaan.preview-page');
        Route::get('/perpustakaan/{material}/send', [PerpustakaanController::class, 'sendToDevice'])->name('perpustakaan.send');
        Route::get('/perpustakaan/{material}/start', [PerpustakaanController::class, 'startMaterial'])->name('perpustakaan.start');
        Route::post('/perpustakaan/{material}/send-material', [PerpustakaanController::class, 'sendMaterialToDevices'])->name('perpustakaan.send-material');
        Route::get('/perpustakaan/{material}/learn', [PerpustakaanController::class, 'learnMaterial'])->name('perpustakaan.learn');
        Route::post('/perpustakaan/{material}/material-page', [PerpustakaanController::class, 'materialPage'])->name('perpustakaan.material-page');
        Route::get('/perpustakaan', [PerpustakaanController::class, 'index'])->name('perpustakaan');
        Route::post('/perpustakaan', [PerpustakaanController::class, 'store'])->name('perpustakaan.store');
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
        
        // Material Management
        Route::get('/manajemen-materi', [MaterialController::class, 'index'])->name('manajemen-materi');
        Route::get('/manajemen-materi/create', [MaterialController::class, 'create'])->name('manajemen-materi.create');
        Route::post('/manajemen-materi', [MaterialController::class, 'store'])->name('manajemen-materi.store');
        Route::get('/manajemen-materi/{material}', [MaterialController::class, 'show'])->name('manajemen-materi.show');
        Route::get('/manajemen-materi/{material}/braille', [MaterialController::class, 'showWithBraille'])->name('manajemen-materi.braille');
        Route::get('/manajemen-materi/{material}/braille-content', [MaterialController::class, 'getBrailleContent'])->name('manajemen-materi.braille-content');
        Route::get('/manajemen-materi/{material}/edit', [MaterialController::class, 'edit'])->name('manajemen-materi.edit');
        Route::put('/manajemen-materi/{material}', [MaterialController::class, 'update'])->name('manajemen-materi.update');
        Route::delete('/manajemen-materi/{material}', [MaterialController::class, 'destroy'])->name('manajemen-materi.destroy');
        Route::post('/manajemen-materi/{material}/reconvert', [MaterialController::class, 'reconvert'])->name('manajemen-materi.reconvert');
        Route::get('/manajemen-materi/{material}/preview', [MaterialController::class, 'preview'])->name('manajemen-materi.preview');
        Route::get('/manajemen-materi/{material}/download-json', [MaterialController::class, 'downloadJson'])->name('manajemen-materi.download-json');
        Route::post('/manajemen-materi/{material}/update-status', [MaterialController::class, 'updateStatus'])->name('manajemen-materi.update-status');
        Route::get('/manajemen-materi/test-conversion', [MaterialController::class, 'testConversion'])->name('manajemen-materi.test-conversion');
        Route::post('/manajemen-materi/{material}/generate-braille', [MaterialController::class, 'generateBraille'])->name('manajemen-materi.generate-braille');
        
        // Device Management
        Route::get('/manajemen-perangkat', [DeviceController::class, 'index'])->name('kelola-perangkat');
        Route::get('/manajemen-perangkat/create', [DeviceController::class, 'create'])->name('kelola-perangkat.create');
        Route::post('/manajemen-perangkat', [DeviceController::class, 'store'])->name('kelola-perangkat.store');
        Route::get('/manajemen-perangkat/{device}/edit', [DeviceController::class, 'edit'])->name('kelola-perangkat.edit');
        Route::put('/manajemen-perangkat/{device}', [DeviceController::class, 'update'])->name('kelola-perangkat.update');
        Route::delete('/manajemen-perangkat/{device}', [DeviceController::class, 'destroy'])->name('kelola-perangkat.destroy');
        Route::post('/manajemen-perangkat/{device}/ping', [DeviceController::class, 'ping'])->name('kelola-perangkat.ping');
        Route::post('/manajemen-perangkat/{device}/status', [DeviceController::class, 'requestStatus'])->name('kelola-perangkat.status');
        Route::get('/manajemen-perangkat/users-by-lembaga', [DeviceController::class, 'getUsersByLembaga'])->name('kelola-perangkat.users-by-lembaga');
        
        // Settings
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.update-profile');
        Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.update-password');
    });
});

// API routes untuk future development
Route::prefix('api')->group(function () {
    Route::get('/lembagas', function () {
        return response()->json(\App\Models\Lembaga::all());
    });
    
    // Temporary route to check access rights options
    Route::get('/check-access-rights', function () {
        $accessOptions = \App\Models\Material::getAksesOptions();
        $lembagas = \App\Models\Lembaga::all();
        
        return response()->json([
            'access_options' => $accessOptions,
            'lembagas' => $lembagas,
            'total_options' => count($accessOptions)
        ]);
    });
});