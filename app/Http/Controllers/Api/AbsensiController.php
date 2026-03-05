<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Absensi\AbsensiService;
use App\Models\{
  Absensi,
  AbsensiLog,
  Karyawan
};
use Carbon\Carbon;

class AbsensiController extends Controller
{
  const RADIUS_METER    = 20;
  const COOLDOWN_MENIT  = 60;
  const MAX_ACCURACY_METER = 50;

  public function clock(Request $request, AbsensiService $service)
  {
    $request->validate([
      'lat' => ['required', 'numeric', 'between:-90,90'],
      'lng' => ['required', 'numeric', 'between:-180,180'],
      'accuracy'  => ['nullable', 'numeric', 'min:0', 'max:1000'],
      'is_mocked' => ['nullable', 'boolean'],
    ]);

    try {
      $user     = $request->user();
      $karyawan = $user->karyawan;
      $isMocked = $request->boolean('is_mocked');
      $accuracy = $request->input('accuracy');

      if ($isMocked) {
        throw new \Exception('Lokasi terdeteksi menggunakan mock/fake GPS.');
      }

      if (!is_null($accuracy) && (float) $accuracy > self::MAX_ACCURACY_METER) {
        throw new \Exception(
          'Akurasi lokasi terlalu rendah (' . round((float) $accuracy, 1) . 'm). Mohon aktifkan GPS lalu coba lagi.'
        );
      }

      if (!$karyawan || $karyawan->status !== 'aktif') {
        throw new \Exception('Karyawan tidak ditemukan atau tidak aktif.');
      }

      if (!Karyawan::where('id', $karyawan->id)->bolehAbsen()->exists()) {
        throw new \Exception('Kategori karyawan tidak diizinkan absen via mobile.');
      }

      // Validasi lokasi
      $lokasi = $karyawan->lokasi;

      if (!$lokasi || is_null($lokasi->lat) || is_null($lokasi->lng)) {
        throw new \Exception('Koordinat lokasi kantor belum diatur.');
      }

      $jarak = $this->haversineDistance($lokasi->lat, $lokasi->lng, $request->lat, $request->lng);

      if ($jarak > self::RADIUS_METER) {
        throw new \Exception(
          "Anda berada di luar area absensi. Jarak: " . round($jarak, 1) . "m, radius: " . self::RADIUS_METER . "m."
        );
      }

      $absensi = Absensi::where('karyawan_id', $karyawan->id)
        ->whereDate('tanggal', now()->toDateString())
        ->first();

      $jam = now()->format('H:i');

      if (!$absensi) {
        $data = [
          'karyawan_id' => $karyawan->id,
          'tanggal'     => now()->toDateString(),
          'status'      => 'hadir',
          'jam_masuk'   => $jam,
        ];
        $absensi = $service->store('mobile', $data);
        $jenis   = 'masuk';
      } elseif ($absensi->jam_masuk && !$absensi->jam_pulang) {
        // Clock-out: cek cooldown dulu
        $sisaCooldown = $this->hitungSisaCooldown($absensi);

        if ($sisaCooldown > 0) {
          throw new \Exception("Silakan tunggu {$sisaCooldown} menit lagi.");
        }

        $absensi->update(['jam_pulang' => $jam]);

        AbsensiLog::create([
          'absensi_id' => $absensi->id,
          'jenis'      => 'pulang',
          'jam'        => $jam,
          'source'     => 'mobile',
          'input_by'   => $user->id,
          'keterangan' => 'Clock-out via mobile',
        ]);

        $jenis = 'pulang';
      } else {
        throw new \Exception('Absensi hari ini sudah lengkap (masuk & pulang).');
      }

      $pesan = $jenis === 'masuk'
        ? 'Absensi masuk berhasil dicatat'
        : 'Absensi pulang berhasil dicatat';

      return response()->json([
        'message' => $pesan,
        'data'    => [
          'jenis'       => $jenis,
          'jam'         => $jam,
          'tanggal'     => now()->toDateString(),
          'jarak_meter' => round($jarak, 1),
          'akurasi_meter' => is_null($accuracy) ? null : round((float) $accuracy, 1),
          'lokasi'      => $lokasi->nama,
        ],
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  public function status(Request $request)
  {
    try {
      $user     = $request->user();
      $karyawan = $user->karyawan;

      if (!$karyawan) {
        throw new \Exception('User tidak memiliki data karyawan.');
      }

      $absensi = Absensi::where('karyawan_id', $karyawan->id)
        ->whereDate('tanggal', now()->toDateString())
        ->first();

      if (!$absensi) {
        return response()->json([
          'message' => 'Status absensi hari ini',
          'data'    => [
            'tanggal'             => now()->toDateString(),
            'status'              => null,
            'jam_masuk'           => null,
            'jam_pulang'          => null,
            'bisa_clock'          => true,
            'clock_berikutnya'    => 'masuk',
            'cooldown_sisa_menit' => 0,
          ],
        ]);
      }

      $bisaClock    = true;
      $clockNext    = null;
      $sisaCooldown = 0;

      if ($absensi->jam_masuk && $absensi->jam_pulang) {
        $bisaClock = false;
      } elseif ($absensi->jam_masuk && !$absensi->jam_pulang) {
        $clockNext    = 'pulang';
        $sisaCooldown = $this->hitungSisaCooldown($absensi);
        $bisaClock    = $sisaCooldown <= 0;
      }

      return response()->json([
        'message' => 'Status absensi hari ini',
        'data'    => [
          'tanggal'             => $absensi->tanggal->format('Y-m-d'),
          'status'              => $absensi->status,
          'jam_masuk'           => $absensi->jam_masuk?->format('H:i'),
          'jam_pulang'          => $absensi->jam_pulang?->format('H:i'),
          'bisa_clock'          => $bisaClock,
          'clock_berikutnya'    => $clockNext,
          'cooldown_sisa_menit' => max(0, $sisaCooldown),
        ],
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  public function history(Request $request)
  {
    $request->validate([
      'from'     => ['nullable', 'date'],
      'to'       => ['nullable', 'date', 'after_or_equal:from'],
      'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    try {
      $user     = $request->user();
      $karyawan = $user->karyawan;

      if (!$karyawan) {
        throw new \Exception('User tidak memiliki data karyawan.');
      }

      $perPage = (int) $request->integer('per_page', 30);

      $query = Absensi::query()
        ->where('karyawan_id', $karyawan->id)
        ->when(
          $request->filled('from'),
          fn($q) =>
          $q->whereDate('tanggal', '>=', $request->input('from'))
        )
        ->when(
          $request->filled('to'),
          fn($q) =>
          $q->whereDate('tanggal', '<=', $request->input('to'))
        )
        ->with([
          'logs' => fn($q) =>
          $q->orderBy('jam', 'asc')->orderBy('id', 'asc')
        ])
        ->orderBy('tanggal', 'desc');

      $records = $query->paginate($perPage);

      $data = $records->getCollection()->map(function (Absensi $absensi) {
        return [
          'id'         => $absensi->id,
          'tanggal'    => $absensi->tanggal?->format('Y-m-d'),
          'status'     => $absensi->status,
          'jam_masuk'  => $absensi->jam_masuk?->format('H:i'),
          'jam_pulang' => $absensi->jam_pulang?->format('H:i'),
          'logs'       => $absensi->logs->map(function (AbsensiLog $log) {
            return [
              'id'         => $log->id,
              'jenis'      => $log->jenis,
              'jam'        => $log->jam?->format('H:i'),
              'source'     => $log->source,
              'keterangan' => $log->keterangan,
            ];
          })->values(),
        ];
      })->values();

      return response()->json([
        'message' => 'Riwayat absensi',
        'data'    => $data,
        'meta'    => [
          'current_page' => $records->currentPage(),
          'last_page'    => $records->lastPage(),
          'per_page'     => $records->perPage(),
          'total'        => $records->total(),
          'has_more'     => $records->hasMorePages(),
        ],
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  /**
   * Hitung sisa cooldown dalam menit.
   */
  private function hitungSisaCooldown(Absensi $absensi): int
  {
    $lastLog = $absensi->logs()
      ->where('jenis', 'masuk')
      ->latest('id')
      ->first();

    if (!$lastLog) {
      return 0;
    }

    $jamLogHariIni = now()->copy()->setTimeFromTimeString((string) $lastLog->jam);

    // Gunakan selisih bertanda: jika jam log berada di masa depan, elapsed = 0
    // agar cooldown tidak naik terus.
    $elapsedMenit = $jamLogHariIni->diffInMinutes(now(), false);
    $elapsedMenit = max(0, $elapsedMenit);
    $sisa         = self::COOLDOWN_MENIT - $elapsedMenit;

    return (int) max(0, $sisa);
  }

  /**
   * Hitung jarak 2 titik koordinat (Haversine), return meter.
   */
  private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
  {
    $R    = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2)
      + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
      * sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $R * $c;
  }
}
