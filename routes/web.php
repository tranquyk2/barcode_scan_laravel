<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeHistoryController;
use Illuminate\Support\Facades\Auth;


Route::middleware(['auth'])->group(function () {
    Route::get('/barcode', function() {
        $histories = \App\Models\BarcodeHistory::where('user_id', auth()->id())->orderByDesc('id')->get();
        return view('barcode', compact('histories'));
    })->name('barcode.index');
    Route::post('/barcode', [BarcodeHistoryController::class, 'store'])->name('barcode.store');
    Route::get('/barcode/export', [BarcodeHistoryController::class, 'export'])->name('barcode.export');
        // Route trang thống kê barcode (đã xóa)
        // Route::get('/statistics', [BarcodeHistoryController::class, 'statistics'])->name('statistics');
    Route::get('/test-ok', function () {
        return 'Laravel đã chạy OK';
    });
});



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
    return view('welcome');
});


Auth::routes();

// Override logout để chuyển về trang login
Route::get('/logout', function() {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
