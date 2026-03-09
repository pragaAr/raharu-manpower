<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
  AuthController,
  AbsensiController,
  PengajuanRequestController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/me', [AuthController::class, 'me']);
  Route::post('/logout', [AuthController::class, 'logout']);

  Route::post('/absensi/clock', [AbsensiController::class, 'clock']);
  Route::get('/absensi/status', [AbsensiController::class, 'status']);
  Route::get('/absensi/history', [AbsensiController::class, 'history']);

  // Pengajuan Cuti
  Route::get('/pengajuan/cuti', [PengajuanRequestController::class, 'cutiIndex']);
  Route::post('/pengajuan/cuti', [PengajuanRequestController::class, 'cutiStore']);
  Route::put('/pengajuan/cuti/{id}', [PengajuanRequestController::class, 'cutiUpdate']);
  Route::delete('/pengajuan/cuti/{id}', [PengajuanRequestController::class, 'cutiDelete']);

  // Pengajuan Tukar Shift
  Route::get('/pengajuan/tukar-shift', [PengajuanRequestController::class, 'tukarShiftIndex']);
  Route::post('/pengajuan/tukar-shift', [PengajuanRequestController::class, 'tukarShiftStore']);
  Route::put('/pengajuan/tukar-shift/{id}', [PengajuanRequestController::class, 'tukarShiftUpdate']);
  Route::delete('/pengajuan/tukar-shift/{id}', [PengajuanRequestController::class, 'tukarShiftDelete']);

  // Pengajuan Perubahan Lembur
  Route::get('/pengajuan/perubahan-lembur', [PengajuanRequestController::class, 'perubahanLemburIndex']);
  Route::post('/pengajuan/perubahan-lembur', [PengajuanRequestController::class, 'perubahanLemburStore']);
  Route::put('/pengajuan/perubahan-lembur/{id}', [PengajuanRequestController::class, 'perubahanLemburUpdate']);
  Route::delete('/pengajuan/perubahan-lembur/{id}', [PengajuanRequestController::class, 'perubahanLemburDelete']);

  // Pengajuan Double Shift
  Route::get('/pengajuan/double-shift', [PengajuanRequestController::class, 'doubleShiftIndex']);
  Route::post('/pengajuan/double-shift', [PengajuanRequestController::class, 'doubleShiftStore']);
  Route::put('/pengajuan/double-shift/{id}', [PengajuanRequestController::class, 'doubleShiftUpdate']);
  Route::delete('/pengajuan/double-shift/{id}', [PengajuanRequestController::class, 'doubleShiftDelete']);
});
