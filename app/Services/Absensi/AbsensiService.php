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
      $data = $this->normalizeByStatus($data);
      $status = $data['status'];

      $absensi = Absensi::create(
        [
          'karyawan_id' => $data['karyawan_id'],
          'tanggal'     => $data['tanggal'],
          'status'      => $data['status'],
          'jam_masuk'   => $data['jam_masuk'] ?? null,
          'jam_pulang'  => $data['jam_pulang'] ?? null,
        ]
      );

      if ($status !== 'hadir') {

        AbsensiLog::create([
          'absensi_id' => $absensi->id,
          'jenis'      => $status,
          'jam'        => now()->format('H:i:s'),
          'source'     => $source,
          'input_by'   => auth()->id(),
          'keterangan' => ucfirst($status),
        ]);

        return $absensi;
      }

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

      $originalStatus = $absensi->status;
      $data = $this->normalizeByStatus($data);
      $status = $data['status'];

      $oldMasuk  = $absensi->jam_masuk;
      $oldPulang = $absensi->jam_pulang;

      $lastLogMasuk = $absensi->logs()->where('jenis', 'masuk')->latest('id')->first();
      $lastLogPulang = $absensi->logs()->where('jenis', 'pulang')->latest('id')->first();

      $oldKeteranganMasuk = $lastLogMasuk?->keterangan;
      $oldKeteranganPulang = $lastLogPulang?->keterangan;

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

      $absensi->update([
        'karyawan_id' => $data['karyawan_id'],
        'tanggal'     => $data['tanggal'],
        'status'      => $data['status'],
        'jam_masuk'   => $data['jam_masuk'],
        'jam_pulang'  => $data['jam_pulang'],
      ]);

      $statusChanged = $status !== $originalStatus;

      if ($status !== 'hadir') {
        if ($statusChanged) {
          AbsensiLog::create([
            'absensi_id' => $absensi->id,
            'jenis'      => $status,
            'jam'        => now()->format('H:i:s'),
            'source'     => $source,
            'input_by'   => auth()->id(),
            'keterangan' => ucfirst($status),
          ]);
        }

        return $absensi;
      }

      if ($status === 'hadir') {
        $jamMasukChanged = isset($data['jam_masuk']) && $data['jam_masuk'] && $data['jam_masuk'] !== $oldMasuk;
        $ketMasukChanged = isset($data['keterangan_masuk']) && $data['keterangan_masuk'] !== $oldKeteranganMasuk;

        if ($jamMasukChanged || $ketMasukChanged) {

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
      }
    });
  }

  private function normalizeByStatus(array $data): array
  {
    if ($data['status'] !== 'hadir') {
      $data['jam_masuk'] = null;
      $data['jam_pulang'] = null;
      $data['keterangan_masuk'] = null;
      $data['keterangan_pulang'] = null;
    }

    return $data;
  }
}
