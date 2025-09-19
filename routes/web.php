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
    // Route admin, chỉ cho phép user có role admin
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/statistics', [BarcodeHistoryController::class, 'statistics'])->name('admin.statistics');
        Route::post('/admin/store', [\App\Http\Controllers\AdminController::class, 'store'])->name('admin.store_user');
        Route::get('/admin/edit/{id}', [\App\Http\Controllers\AdminController::class, 'edit'])->name('admin.edit_user');
        Route::put('/admin/update/{id}', [\App\Http\Controllers\AdminController::class, 'update'])->name('admin.update_user');
        Route::delete('/admin/delete/{id}', [\App\Http\Controllers\AdminController::class, 'destroy'])->name('admin.delete_user');
        Route::get('/admin/histories', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.histories');
        Route::get('/admin/export-histories', [\App\Http\Controllers\AdminController::class, 'exportHistory'])->name('admin.export_histories');
    });
    Route::get('/admin/create', [\App\Http\Controllers\AdminController::class, 'create'])->name('admin.create_user');
    Route::post('/admin/store', [\App\Http\Controllers\AdminController::class, 'store'])->name('admin.store_user');
    Route::get('/admin/edit/{id}', [\App\Http\Controllers\AdminController::class, 'edit'])->name('admin.edit_user');
    Route::put('/admin/update/{id}', [\App\Http\Controllers\AdminController::class, 'update'])->name('admin.update_user');
    Route::delete('/admin/delete/{id}', [\App\Http\Controllers\AdminController::class, 'destroy'])->name('admin.delete_user');
    Route::get('/admin/histories', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.histories');
    Route::get('/admin/export-histories', [\App\Http\Controllers\AdminController::class, 'exportHistory'])->name('admin.export_histories');
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
    return redirect()->route('login');
});


Auth::routes();

// Override logout để chuyển về trang login
Route::get('/logout', function() {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
