<?php

namespace App\Services\Absensi;

use Illuminate\Support\Facades\DB;
use App\Models\{
  Absensi,
  AbsensiLog
};

class AbsensiService
{
  public function store(string $source, array $data): Absensi
  {
    return DB::transaction(function () use ($source, $data) {

      $absensi = Absensi::create(
        [
          'karyawan_id' => $data['karyawan_id'],
          'tanggal'     => $data['tanggal'],
          'jam_masuk'   => $data['jam_masuk'] ?? null,
          'jam_pulang'  => $data['jam_pulang'] ?? null,
        ]
      );

      // Log Masuk
      if (!empty($data['jam_masuk'])) {
        AbsensiLog::create([
          'absensi_id' => $absensi->id,
          'jenis'      => 'masuk',
          'jam'        => $data['jam_masuk'],
          'source'     => $source,
          'input_by'   => auth()->id(),
          'keterangan' => $data['keterangan_masuk'] ?? null,
        ]);
      }

      // Log Pulang
      if (!empty($data['jam_pulang'])) {
        AbsensiLog::create([
          'absensi_id' => $absensi->id,
          'jenis'      => 'pulang',
          'jam'        => $data['jam_pulang'],
          'source'     => $source,
          'input_by'   => auth()->id(),
          'keterangan' => $data['keterangan_pulang'] ?? null,
        ]);
      }

      return $absensi;
    });
  }

  public function update(int $absensiId, string $source, array $data): Absensi
  {
    return DB::transaction(function () use ($absensiId, $source, $data) {

      $absensi = Absensi::with('logs')->findOrFail($absensiId);

      $oldMasuk  = $absensi->jam_masuk;
      $oldPulang = $absensi->jam_pulang;

      // Get latest logs to compare keterangan
      $lastLogMasuk = $absensi->logs()->where('jenis', 'masuk')->latest('id')->first();
      $lastLogPulang = $absensi->logs()->where('jenis', 'pulang')->latest('id')->first();

      $oldKeteranganMasuk = $lastLogMasuk?->keterangan;
      $oldKeteranganPulang = $lastLogPulang?->keterangan;

      /*
        |--------------------------------------------------------------------------
        | 1️⃣ Cek Duplicate jika karyawan/tanggal berubah
        |--------------------------------------------------------------------------
        */

      if (
        $absensi->karyawan_id != $data['karyawan_id'] ||
        $absensi->tanggal != $data['tanggal']
      ) {
        $exists = Absensi::where('karyawan_id', $data['karyawan_id'])
          ->whereDate('tanggal', $data['tanggal'])
          ->where('id', '!=', $absensi->id)
          ->exists();

        if ($exists) {
          throw new \Exception('Absensi untuk karyawan dan tanggal tersebut sudah ada.');
        }
      }

      /*
        |--------------------------------------------------------------------------
        | 2️⃣ Update Main Record (Identity + State)
        |--------------------------------------------------------------------------
        */

      $absensi->update([
        'karyawan_id' => $data['karyawan_id'],
        'tanggal'     => $data['tanggal'],
        'jam_masuk'   => $data['jam_masuk'] ?? $absensi->jam_masuk,
        'jam_pulang'  => $data['jam_pulang'] ?? $absensi->jam_pulang,
      ]);

      /*
        |--------------------------------------------------------------------------
        | 3️⃣ Log Jika Jam ATAU Keterangan Berubah
        |--------------------------------------------------------------------------
        */

      // Cek perubahan Masuk
      $jamMasukChanged = isset($data['jam_masuk']) && $data['jam_masuk'] && $data['jam_masuk'] !== $oldMasuk;
      $ketMasukChanged = isset($data['keterangan_masuk']) && $data['keterangan_masuk'] !== $oldKeteranganMasuk;

      if ($jamMasukChanged || $ketMasukChanged) {
        // Jika jam dihapus/null, jangan buat log baru (logic existing sepertinya mengizinkan null jam_masuk?)
        // Revisi: jam_masuk di update boleh null??
        // Di validation rule: masuk => nullable.
        // Jika data['jam_masuk'] ada isinya, kita log.

        if (!empty($data['jam_masuk'])) {
          AbsensiLog::create([
            'absensi_id' => $absensi->id,
            'jenis'      => 'masuk',
            'jam'        => $data['jam_masuk'],
            'source'     => $source,
            'input_by'   => auth()->id(),
            'keterangan' => $data['keterangan_masuk'] ?? null,
          ]);
        }
      }

      // Cek perubahan Pulang
      $jamPulangChanged = isset($data['jam_pulang']) && $data['jam_pulang'] && $data['jam_pulang'] !== $oldPulang;
      $ketPulangChanged = isset($data['keterangan_pulang']) && $data['keterangan_pulang'] !== $oldKeteranganPulang;

      if ($jamPulangChanged || $ketPulangChanged) {
        if (!empty($data['jam_pulang'])) {
          AbsensiLog::create([
            'absensi_id' => $absensi->id,
            'jenis'      => 'pulang',
            'jam'        => $data['jam_pulang'],
            'source'     => $source,
            'input_by'   => auth()->id(),
            'keterangan' => $data['keterangan_pulang'] ?? null,
          ]);
        }
      }

      return $absensi;
    });
  }
}
