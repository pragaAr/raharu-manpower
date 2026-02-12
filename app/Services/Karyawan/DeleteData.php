<?php

namespace App\Services\Karyawan;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class DeleteData
{
  public function delete(int $karyawanId): void
  {
    DB::transaction(function () use ($karyawanId) {
      $karyawan = Karyawan::findOrFail($karyawanId);

      if (
        $karyawan->img &&
        $karyawan->img !== 'uploads/default.webp' &&
        file_exists(public_path($karyawan->img))
      ) {
        unlink(public_path($karyawan->img));
      }

      $karyawan->delete();
    });
  }
}
