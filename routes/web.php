<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

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
Route::get('/createDecipherTask', [TaskController::class, 'createDecipherTask'])->name('tasks.createDecipherTask');
