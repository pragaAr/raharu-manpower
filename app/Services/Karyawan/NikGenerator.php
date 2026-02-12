<?php

namespace App\Services\Karyawan;

use App\Models\{
  Karyawan,
  Lokasi,
  Jabatan
};

class NikGenerator
{
  public function generate(Lokasi $lokasi, Jabatan $jabatan): string
  {
    $prefix = strtolower($lokasi->kode . $jabatan->unit->divisi->kode . $jabatan->unit->kode);

    $lastNik = Karyawan::select('nik')
      ->orderBy('id', 'desc')
      ->value('nik');

    $lastNumber = 0;

    if ($lastNik) {
      $parts      = explode('-', $lastNik);
      $lastNumber = (int) end($parts);
    }

    $nextNumber = $lastNumber + 1;

    return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
  }

  public function forMutasi(Karyawan $karyawan, Lokasi $lokasi, Jabatan $jabatan): string
  {
    [, $number] = explode('-', $karyawan->nik);

    $prefix = strtolower($lokasi->kode . $jabatan->unit->divisi->kode . $jabatan->unit->kode);

    return $prefix . '-' . $number;
  }

  public function forProbation(Lokasi $lokasi): string
  {
    $prefix = strtolower($lokasi->kode . 'prob');

    $lastNik = Karyawan::select('nik')
      ->orderBy('id', 'desc')
      ->value('nik');

    $lastNumber = 0;

    if ($lastNik) {
      $parts      = explode('-', $lastNik);
      $lastNumber = (int) end($parts);
    }

    $nextNumber = $lastNumber + 1;

    return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
  }

  // karyawan keluar (nonaktif) masuk lagi, menjadi probation
  public function forProbationFromExisting(Karyawan $karyawan, Lokasi $lokasi): string
  {
    if (! $karyawan->nik) {
      throw new \LogicException('Karyawan belum memiliki NIK');
    }

    $parts  = explode('-', $karyawan->nik);
    $number = end($parts);

    $prefix = strtolower(str_replace(' ', '', $lokasi->kode) . 'prob');

    return $prefix . '-' . $number;
  }
}
