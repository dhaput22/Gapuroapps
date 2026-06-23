<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FgDeliveryController;
use App\Http\Controllers\FgReceivingController;
use App\Http\Controllers\FgDisposeController;
use App\Http\Controllers\FgReturnController;
use App\Http\Controllers\FgStockController;
use App\Http\Controllers\FgSummaryDeliveryController;
use App\Http\Controllers\FgSummaryStockController;
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

    Route::middleware('role:admin')->group(function () {
        Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
        Route::post('/operators', [OperatorController::class, 'store'])->name('operators.store');
        Route::get('/operators/preview', [OperatorController::class, 'preview'])->name('operators.preview');
        Route::get('/operators/{operator}/edit', [OperatorController::class, 'edit'])->name('operators.edit');
        Route::put('/operators/{operator}', [OperatorController::class, 'update'])->name('operators.update');
        Route::delete('/operators/{operator}', [OperatorController::class, 'destroy'])->name('operators.destroy');
    });

    Route::get('/fg-storage', [FgDeliveryController::class, 'index'])->name('fg.storage');

    Route::get('/fg-storage/receiving', [FgReceivingController::class, 'index'])->name('fg.storage.receiving');
    Route::get('/fg-storage/receiving/create-unregistered', [FgReceivingController::class, 'createUnregistered'])->name('fg.storage.receiving.create-unregistered');
    Route::get('/fg-storage/receiving/create-unregistered/preview-part', [FgReceivingController::class, 'previewUnregisteredPart'])->name('fg.storage.receiving.create-unregistered.preview-part');
    Route::get('/fg-storage/receiving/create-unregistered/preview', [FgReceivingController::class, 'previewUnregisteredPlan'])->name('fg.storage.receiving.create-unregistered.preview');
    Route::post('/fg-storage/receiving/create-unregistered', [FgReceivingController::class, 'storeUnregistered'])->name('fg.storage.receiving.create-unregistered.store');
    Route::get('/fg-storage/receiving/{scan}/edit', [FgReceivingController::class, 'edit'])->middleware('role:admin')->name('fg.storage.receiving.edit');
    Route::put('/fg-storage/receiving/{scan}', [FgReceivingController::class, 'update'])->middleware('role:admin')->name('fg.storage.receiving.update');
    Route::delete('/fg-storage/receiving/{scan}', [FgReceivingController::class, 'destroy'])->middleware('role:admin')->name('fg.storage.receiving.destroy');

    Route::get('/fg-storage/stock', [FgStockController::class, 'index'])->name('fg.storage.stock');
    Route::get('/fg-storage/stock/{scan}/edit', [FgStockController::class, 'edit'])->middleware('role:admin')->name('fg.storage.stock.edit');
    Route::put('/fg-storage/stock/{scan}', [FgStockController::class, 'update'])->middleware('role:admin')->name('fg.storage.stock.update');
    Route::delete('/fg-storage/stock/{scan}', [FgStockController::class, 'destroy'])->middleware('role:admin')->name('fg.storage.stock.destroy');

    Route::get('/fg-storage/swa', [FgSwaPlanController::class, 'index'])->name('fg.storage.swa');
    Route::get('/fg-storage/swa/create', [FgSwaPlanController::class, 'create'])->name('fg.storage.swa.create');
    Route::post('/fg-storage/swa', [FgSwaPlanController::class, 'store'])->name('fg.storage.swa.store');
    Route::get('/fg-storage/swa/{plan}/edit', [FgSwaPlanController::class, 'edit'])
        ->middleware('role:admin')
        ->name('fg.storage.swa.edit');
    Route::put('/fg-storage/swa/{plan}', [FgSwaPlanController::class, 'update'])
        ->middleware('role:admin')
        ->name('fg.storage.swa.update');
    Route::delete('/fg-storage/swa/{plan}', [FgSwaPlanController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('fg.storage.swa.destroy');

    Route::get('/fg-storage/delivery-scan', [FgDeliveryController::class, 'createScan'])->name('fg.storage.delivery.scan');
    Route::get('/fg-storage/delivery-scan/preview-part', [FgDeliveryController::class, 'previewPart'])->name('fg.storage.delivery.scan.preview-part');
    Route::get('/fg-storage/delivery-scan/preview', [FgDeliveryController::class, 'previewScan'])->name('fg.storage.delivery.scan.preview');
    Route::post('/fg-storage/delivery-scan', [FgDeliveryController::class, 'storeScan'])->name('fg.storage.delivery.scan.store');
    Route::get('/fg-storage/delivery/{scan}/edit', [FgDeliveryController::class, 'edit'])->middleware('role:admin')->name('fg.storage.delivery.edit');
    Route::put('/fg-storage/delivery/{scan}', [FgDeliveryController::class, 'update'])->middleware('role:admin')->name('fg.storage.delivery.update');
    Route::delete('/fg-storage/delivery/{scan}', [FgDeliveryController::class, 'destroy'])->middleware('role:admin')->name('fg.storage.delivery.destroy');

    Route::get('/fg-storage/return', [FgReturnController::class, 'index'])->name('fg.storage.return.index');
    Route::get('/fg-storage/return/create', [FgReturnController::class, 'create'])->name('fg.storage.return.create');
    Route::get('/fg-storage/return/preview-part', [FgReturnController::class, 'previewPart'])->name('fg.storage.return.preview-part');
    Route::get('/fg-storage/return/preview', [FgReturnController::class, 'previewScan'])->name('fg.storage.return.preview');
    Route::post('/fg-storage/return', [FgReturnController::class, 'store'])->name('fg.storage.return.store');
    Route::get('/fg-storage/return/{scan}/edit', [FgReturnController::class, 'edit'])->middleware('role:admin')->name('fg.storage.return.edit');
    Route::put('/fg-storage/return/{scan}', [FgReturnController::class, 'update'])->middleware('role:admin')->name('fg.storage.return.update');
    Route::delete('/fg-storage/return/{scan}', [FgReturnController::class, 'destroy'])->middleware('role:admin')->name('fg.storage.return.destroy');

    Route::get('/fg-storage/summary-stock', [FgSummaryStockController::class, 'index'])->name('fg.storage.summary-stock');
    Route::get('/fg-storage/summary-stock/export', [FgSummaryStockController::class, 'exportExcel'])->name('fg.storage.summary-stock.export');
    Route::get('/fg-storage/summary-delivery', [FgSummaryDeliveryController::class, 'index'])->name('fg.storage.summary-delivery');
    Route::get('/fg-storage/summary-delivery/export', [FgSummaryDeliveryController::class, 'exportExcel'])->name('fg.storage.summary-delivery.export');

    Route::get('/fg-storage/dispose', [FgDisposeController::class, 'index'])->name('fg.storage.dispose.index');
    Route::get('/fg-storage/dispose/create', [FgDisposeController::class, 'create'])->name('fg.storage.dispose.create');
    Route::get('/fg-storage/dispose/preview-part', [FgDisposeController::class, 'previewPart'])->name('fg.storage.dispose.preview-part');
    Route::get('/fg-storage/dispose/preview', [FgDisposeController::class, 'previewScan'])->name('fg.storage.dispose.preview');
    Route::post('/fg-storage/dispose', [FgDisposeController::class, 'store'])->name('fg.storage.dispose.store');
    Route::get('/fg-storage/dispose/{scan}/edit', [FgDisposeController::class, 'edit'])->middleware('role:admin')->name('fg.storage.dispose.edit');
    Route::put('/fg-storage/dispose/{scan}', [FgDisposeController::class, 'update'])->middleware('role:admin')->name('fg.storage.dispose.update');
    Route::delete('/fg-storage/dispose/{scan}', [FgDisposeController::class, 'destroy'])->middleware('role:admin')->name('fg.storage.dispose.destroy');

    Route::get('/material-storage', function () {
        return view('material-storage.index');
    })->name('material.storage');

    Route::get('/material-storage/scan', function () {
        return view('material-storage.scan');
    })->name('material.storage.scan');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('admin.users.deactivate');
    Route::post('/admin/users/{user}/activate', [AdminUserController::class, 'activate'])->name('admin.users.activate');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});


require __DIR__ . '/auth.php';
