<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
  AuthController,
  AbsensiController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/me', [AuthController::class, 'me']);
  Route::post('/logout', [AuthController::class, 'logout']);

  Route::post('/absensi/clock', [AbsensiController::class, 'clock']);
  Route::get('/absensi/status', [AbsensiController::class, 'status']);
  Route::get('/absensi/history', [AbsensiController::class, 'history']);
});
