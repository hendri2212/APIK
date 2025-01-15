<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PresenceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});
Route::view('/absent', 'absent');

// routes/web.php
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/checkin', [PresenceController::class, 'CheckIn']);
Route::get('/checkout', [PresenceController::class, 'CheckOut']);
Route::get('/history/absent', [App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');