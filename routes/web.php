<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\HistoryController;

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
})->name('dashboard');
// Route::view('/presence')->name('presence');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/presence', [PresenceController::class, 'index'])->name('presence');
Route::get('/checkin', [PresenceController::class, 'CheckIn']);
Route::get('/checkout', [PresenceController::class, 'CheckOut']);
Route::get('/face/{face}', [FaceController::class, 'show'])->name('face.show');
Route::get('/face', [FaceController::class, 'index']);
Route::get('/history/absent', [App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');