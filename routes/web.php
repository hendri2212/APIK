<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\WorkLocationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TodayController;
use App\Http\Controllers\JamAbsenController;
// use Closure;

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
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::middleware(['checkAuth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/presence', [PresenceController::class, 'index'])->name('presence');
    Route::get('/checkin', [PresenceController::class, 'CheckIn']);
    Route::get('/checkout', [PresenceController::class, 'CheckOut']);
    Route::prefix('face')->group(function () {
        Route::view('/add', 'face.add');
        Route::get('/', [FaceController::class, 'index'])->name('face.data');
        Route::post('/', [FaceController::class, 'store'])->name('face.store');
        Route::get('/{id}', [FaceController::class, 'edit'])->where('id', '[0-9]+');
        Route::get('/{file_name}', [FaceController::class, 'show'])->name('face.show')->where('file_name', '[a-zA-Z0-9_\-\.]+');
        Route::put('/{id}', [FaceController::class, 'update'])->name('face.update');
        Route::delete('/{id}', [FaceController::class, 'destroy'])->name('face.delete');
    });
    Route::get('/schedule', [JamAbsenController::class, 'index'])->name('schedule');
    Route::put('/schedule', [JamAbsenController::class, 'update'])->name('schedule.update');
    Route::get('/history/absent', [App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
    Route::get('/today', [TodayController::class, 'index'])->name('history.today');
    Route::get('/workplace', [WorkLocationController::class, 'index']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['isAdminUser'])->group(function () {
        Route::resource('members', MemberController::class);
    });
});

Route::get('/test-wa', function (\Illuminate\Http\Request $request) {
    $no_hp = $request->query('no_hp');
    if (!$no_hp) {
        return 'Please provide no_hp query parameter. Example: /test-wa?no_hp=08123456789';
    }

    $message = 'Test WhatsApp Notification from Debug Route';
    $payload = [
        'to' => $no_hp,
        'message' => $message
    ];

    try {
        $response = \Illuminate\Support\Facades\Http::post('https://wabot.tukarjual.com/send', $payload);
        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
            'successful' => $response->successful()
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage()
        ];
    }
});