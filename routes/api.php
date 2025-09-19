<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
Route::post('/login', [\App\Http\Controllers\ApiLoginController::class, 'login']);
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [\App\Http\Controllers\ApiLoginController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
	// Quét barcode (API)
	Route::post('/barcode/scan', [\App\Http\Controllers\BarcodeApiController::class, 'scan']);
	// Lấy lịch sử quét của user
	Route::get('/barcode/my-history', [\App\Http\Controllers\BarcodeApiController::class, 'myHistory']);
	// Các route cũ nếu cần giữ lại
	Route::post('/barcode-history', [\App\Http\Controllers\BarcodeHistoryController::class, 'store']);
	Route::get('/barcode-history', [\App\Http\Controllers\BarcodeHistoryController::class, 'index']);
});
