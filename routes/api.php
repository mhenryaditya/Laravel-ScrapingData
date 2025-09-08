<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EdurankMPeringkatController;
use App\Http\Controllers\SintaMPeringkatController;
use App\Http\Controllers\UIGMMPeringkatController;
use App\Http\Controllers\UIGMRMetriksController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to API-ScrapingData',
        'version' => '1.0.0',
        'status' => 'active',
    ], 200);
});

// SINTA
Route::post('sinta/import', [SintaMPeringkatController::class, 'import']);
Route::get('sinta/deleted', [SintaMPeringkatController::class, 'showSoftDelete']);
Route::get('sinta/deleted/{id}', [SintaMPeringkatController::class, 'restoreSoftDelete']);
Route::delete('sinta/deleted/{id}', [SintaMPeringkatController::class, 'permanetDelSoftDelete']);
Route::resource('sinta', SintaMPeringkatController::class);

// UIGM M Peringkat
Route::post('uigm/mpr/import', [UIGMMPeringkatController::class, 'import']);
Route::get('uigm/mpr/deleted', [UIGMMPeringkatController::class, 'showSoftDelete']);
Route::get('uigm/mpr/deleted/{id}', [UIGMMPeringkatController::class, 'restoreSoftDelete']);
Route::delete('uigm/mpr/deleted/{id}', [UIGMMPeringkatController::class, 'permanetDelSoftDelete']);
Route::resource('uigm/mpr', UIGMMPeringkatController::class);

// UIGM R Metriks
// Route::post('uigm/rme/import', [UIGMRMetriksController::class, 'import']);
Route::get('uigm/rme/deleted', [UIGMRMetriksController::class, 'showSoftDelete']);
Route::get('uigm/rme/deleted/{id}', [UIGMRMetriksController::class, 'restoreSoftDelete']);
Route::delete('uigm/rme/deleted/{id}', [UIGMRMetriksController::class, 'permanetDelSoftDelete']);
Route::resource('uigm/rme', UIGMRMetriksController::class);

// Edurank
Route::post('edurank/import', [EdurankMPeringkatController::class, 'import']);
Route::get('edurank/deleted', [EdurankMPeringkatController::class, 'showSoftDelete']);
Route::get('edurank/deleted/{id}', [EdurankMPeringkatController::class, 'restoreSoftDelete']);
Route::delete('edurank/deleted/{id}', [EdurankMPeringkatController::class, 'permanetDelSoftDelete']);
Route::resource('edurank', EdurankMPeringkatController::class);

// user
// Route::post('auth/login', [AuthController::class, 'login']);
// Route::post('auth/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
// Route::post('auth/refresh', [AuthController::class, 'refresh']);
// Route::post('user/register', [AuthController::class, 'register'])->middleware(['auth:api']);
// Route::get('user/profile/{filename}', [AuthController::class, 'getProfileImage'])->middleware(['auth:api']);

