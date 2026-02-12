<?php

namespace App\Services\Karyawan;

use App\Models\{
  Karyawan,
  Kontrak,
  History
};
use Illuminate\Support\Facades\DB;

class Renewalkontrak
{
  public function renewal(array $data, array $new): bool
  {
    return DB::transaction(function () use ($data, $new) {

      $karyawan = Karyawan::with(
        'kategori',
        'kontrakTerakhir'
      )->findOrFail($data['karyawanId']);

      if ($karyawan->kategori?->nama !== 'pkwt') {
        return false;
      }

      $oldData = [
        'tgl_efektif' => optional($karyawan->tgl_masuk)?->format('Y-m-d'),
        'tgl_mulai'   => optional($karyawan->kontrakTerakhir?->tgl_mulai)?->format('Y-m-d'),
        'tgl_selesai' => optional($karyawan->kontrakTerakhir?->tgl_selesai)?->format('Y-m-d'),
      ];

      $newData = array_filter([
        'tgl_efektif' => $new['efektif'] ?? null,
        'tgl_mulai'   => $new['tmk'] ?? null,
        'tgl_selesai' => $new['thk'] ?? null,
      ], fn($v) => filled($v));

      $update = array_diff_assoc($newData, $oldData);

      if (empty($update)) {
        return false;
      }

      $karyawan->update($update);

      History::create([
        'jenis'         => 'renewal',
        'karyawan_id'   => $karyawan->id,
        'nama_karyawan' => $karyawan->nama,
        'tgl_masuk'     => $new['efektif'] ?? null,
        'tgl_mulai'     => $new['tmk'] ?? null,
        'tgl_selesai'   => $new['thk'] ?? null,
        'status'        => $karyawan->status ?? null,
        'keterangan'    => $data['keterangan'] ?? 'renewal kontrak',
      ]);

      if ($karyawan->kontrakTerakhir) {
        $karyawan->kontrakTerakhir->update([
          'status' => 'berakhir',
        ]);
      }

      $kontrakKe = Kontrak::where('karyawan_id', $karyawan->id)->max('kontrak_ke') ?? 0;

      $this->handleKontrak($karyawan, $kontrakKe, $new);

      return true;
    });
  }

  protected function handleKontrak(Karyawan $karyawan, int $kontrak, array $new): void
  {
    $kontrakKe = $kontrak + 1;

    Kontrak::create([
      'karyawan_id'   => $karyawan->id,
      'nama_karyawan' => $karyawan->nama,
      'kontrak_ke'    => $kontrakKe,
      'tgl_mulai'     => $new['tmk'] ?? null,
      'tgl_selesai'   => $new['thk'] ?? null,
      'status'        => 'aktif',
    ]);
  }
}
