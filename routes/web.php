<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DecipherExportController;
use App\Http\Controllers\SecureDownloadController;

// Authentication Routes (Laravel UI)
Auth::routes();

// Public Routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('tasks.index');
    }
    return view('welcome');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', function () {
        return view('auth.profile', ['user' => Auth::user()]);
    })->name('profile');
    
    // Task Routes
    Route::get('/index', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/{task}/force', [TaskController::class, 'force'])->name('tasks.force');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Decipher Export Routes
    Route::get('/decipherExport/{task}/view', [DecipherExportController::class, 'view'])->name('decipherExport.view');
    Route::get('/decipherExport/{task}/edit', [DecipherExportController::class, 'edit'])->name('decipherExport.edit');
    Route::put('/decipherExport/{task}', [DecipherExportController::class, 'update'])->name('decipherExport.update');
    Route::get('/createDecipherTask', [DecipherExportController::class, 'createDecipherTask'])->name('decipherExport.createDecipherTask');
    Route::post('/decipherExport', [DecipherExportController::class, 'store'])->name('decipherExport.store');
});

// Secure Download Routes
Route::get('/download/{slug}', [SecureDownloadController::class, 'showPasswordForm'])
    ->name('secure-download.form');
Route::post('/download/{slug}/verify', [SecureDownloadController::class, 'verifyPassword'])
    ->name('secure-download.verify');
Route::get('/download/{slug}/file', [SecureDownloadController::class, 'download'])
    ->name('secure-download.download');
