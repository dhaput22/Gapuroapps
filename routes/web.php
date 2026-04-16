<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FgDeliveryController;
use App\Http\Controllers\FgReceivingController;
use App\Http\Controllers\FgSwaPlanController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('/auth/login');
});

// Dashboard
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// FG Storage
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/fg-storage-metrics', [DashboardController::class, 'fgStorageMetrics'])->name('dashboard.fg-storage.metrics');

    Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    Route::post('/operators', [OperatorController::class, 'store'])->name('operators.store');
    Route::delete('/operators/{operator}', [OperatorController::class, 'destroy'])->name('operators.destroy');
    Route::get('/operators/preview', [OperatorController::class, 'preview'])->name('operators.preview');

    Route::get('/fg-storage', [FgDeliveryController::class, 'index'])->name('fg.storage');

    Route::get('/fg-storage/receiving', [FgReceivingController::class, 'index'])->name('fg.storage.receiving');
    Route::get('/fg-storage/receiving/create-unregistered', [FgReceivingController::class, 'createUnregistered'])->name('fg.storage.receiving.create-unregistered');
    Route::get('/fg-storage/receiving/create-unregistered/preview-part', [FgReceivingController::class, 'previewUnregisteredPart'])->name('fg.storage.receiving.create-unregistered.preview-part');
    Route::get('/fg-storage/receiving/create-unregistered/preview', [FgReceivingController::class, 'previewUnregisteredPlan'])->name('fg.storage.receiving.create-unregistered.preview');
    Route::post('/fg-storage/receiving/create-unregistered', [FgReceivingController::class, 'storeUnregistered'])->name('fg.storage.receiving.create-unregistered.store');

    Route::get('/fg-storage/stock', function () {
        return view('fg-storage.stock');
    })->name('fg.storage.stock');

    Route::get('/fg-storage/swa', [FgSwaPlanController::class, 'index'])->name('fg.storage.swa');
    Route::get('/fg-storage/swa/create', [FgSwaPlanController::class, 'create'])->name('fg.storage.swa.create');
    Route::post('/fg-storage/swa', [FgSwaPlanController::class, 'store'])->name('fg.storage.swa.store');
    Route::get('/fg-storage/swa/{plan}/edit', [FgSwaPlanController::class, 'edit'])
        ->middleware('role:super_admin,staff,supervisor')
        ->name('fg.storage.swa.edit');
    Route::put('/fg-storage/swa/{plan}', [FgSwaPlanController::class, 'update'])
        ->middleware('role:super_admin,staff,supervisor')
        ->name('fg.storage.swa.update');
    Route::delete('/fg-storage/swa/{plan}', [FgSwaPlanController::class, 'destroy'])
        ->middleware('role:super_admin,staff,supervisor')
        ->name('fg.storage.swa.destroy');

    Route::get('/fg-storage/delivery-scan', [FgDeliveryController::class, 'createScan'])->name('fg.storage.delivery.scan');
    Route::get('/fg-storage/delivery-scan/preview-part', [FgDeliveryController::class, 'previewPart'])->name('fg.storage.delivery.scan.preview-part');
    Route::get('/fg-storage/delivery-scan/preview', [FgDeliveryController::class, 'previewScan'])->name('fg.storage.delivery.scan.preview');
    Route::post('/fg-storage/delivery-scan', [FgDeliveryController::class, 'storeScan'])->name('fg.storage.delivery.scan.store');

    Route::get('/material-storage', function () {
        return view('material-storage.index');
    })->name('material.storage');

    Route::get('/material-storage/scan', function () {
        return view('material-storage.scan');
    })->name('material.storage.scan');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
});


require __DIR__ . '/auth.php';
