<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('/auth/login');
});

// Dashboard
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Register
Route::middleware('web', 'guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
});

// FG Storage
Route::middleware('auth')->group(function () {
    Route::get('/fg-storage', function () {
        return view('fg-storage.delivery');
    })->name('fg.storage');

    Route::get('/fg-storage/receiving', function () {
        return view('fg-storage.receiving');
    })->name('fg.storage.receiving');

    Route::get('/fg-storage/receiving/create-unregistered', function () {
        return view('fg-storage.receiving-unregistered');
    })->name('fg.storage.receiving.create-unregistered');

    Route::get('/fg-storage/stock', function () {
        return view('fg-storage.stock');
    })->name('fg.storage.stock');

    Route::get('/fg-storage/swa', function () {
        return view('fg-storage.swa');
    })->name('fg.storage.swa');

    Route::get('/fg-storage/delivery-scan', function () {
        return view('fg-storage.index');
    })->name('fg.storage.delivery.scan');

    Route::get('/material-storage', function () {
        return view('material-storage.index');
    })->name('material.storage');

    Route::get('/material-storage/scan', function () {
        return view('material-storage.scan');
    })->name('material.storage.scan');
});


require __DIR__ . '/auth.php';
