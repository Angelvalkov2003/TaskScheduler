<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DecipherExportController;
use App\Http\Controllers\SecureDownloadController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegister')->name('show.register');
    Route::get('/login', 'showLogin')->name('show.login');
    Route::post('/register', 'Register')->name('register');
    Route::post('/login', 'Login')->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'Profile'])->name('profile');
});

Route::get('/index', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/decipherExport/{task}/view', [DecipherExportController::class, 'view'])->name('decipherExport.view');
Route::get('/decipherExport/{task}/edit', [DecipherExportController::class, 'edit'])->name('decipherExport.edit');
Route::put('/decipherExport/{task}', [DecipherExportController::class, 'update'])->name('decipherExport.update');
Route::post('/tasks/{task}/force', [TaskController::class, 'force'])->name('tasks.force');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::get('/createDecipherTask', [DecipherExportController::class, 'createDecipherTask'])->name('decipherExport.createDecipherTask');
Route::post('/decipherExport', [DecipherExportController::class, 'store'])->name('decipherExport.store');

// Secure Download Routes
Route::get('/download/{slug}', [SecureDownloadController::class, 'showPasswordForm'])
    ->name('secure-download.form');
Route::post('/download/{slug}/verify', [SecureDownloadController::class, 'verifyPassword'])
    ->name('secure-download.verify');
Route::get('/download/{slug}/file', [SecureDownloadController::class, 'download'])
    ->name('secure-download.download');