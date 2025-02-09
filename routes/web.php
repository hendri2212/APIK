<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\WorkLocationController;

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

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/presence', [PresenceController::class, 'index'])->name('presence');
Route::get('/checkin', [PresenceController::class, 'CheckIn']);
Route::get('/checkout', [PresenceController::class, 'CheckOut']);
Route::prefix('face')->group(function () {
    Route::view('/add', 'face.add');
    Route::get('/', [FaceController::class, 'index']);
    Route::post('/', [FaceController::class, 'store'])->name('face.store');
    Route::get('/{id}', [FaceController::class, 'edit'])->where('id', '[0-9]+');
    Route::get('/{file_name}', [FaceController::class, 'show'])->name('face.show')->where('file_name', '[a-zA-Z0-9_\-\.]+');
    Route::put('/{id}', [FaceController::class, 'update'])->name('face.update');
    Route::delete('/{id}', [FaceController::class, 'destroy'])->name('face.delete');
});
Route::get('/history/absent', [App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
Route::get('/workplace', [WorkLocationController::class, 'index']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');