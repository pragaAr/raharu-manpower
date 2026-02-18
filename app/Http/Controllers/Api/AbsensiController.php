<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Absensi\AbsensiService;
use App\Models\AbsensiLog;

class AbsensiController extends Controller
{
  public function store(Request $request, AbsensiService $service)
  {
    $data = $request->validate([
      'karyawan_id'       => ['required', 'exists:karyawan,id'],
      'tanggal'           => ['required', 'date'],
      'jam_masuk'         => ['nullable', 'date_format:H:i'],
      'keterangan_masuk'  => ['nullable', 'string'],
      'jam_pulang'        => ['nullable', 'date_format:H:i'],
      'keterangan_pulang' => ['nullable', 'string'],
    ]);

    try {
      $absensi = $service->store('api', $data);

      return response()->json([
        'message' => 'Absensi berhasil disimpan',
        'data' => $absensi
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Terjadi kesalahan saat menyimpan absensi',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
