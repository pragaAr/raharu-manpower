<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
  public function run(): void
  {
    Karyawan::create([
      'kategori_id' => 1,
      'lokasi_id' => 1,
      'jabatan_id' => 16,
      'nik' => 'tst-0000',
      'nama' => 'john doe',
      'ktp' => '1234567890123456',
      'agama' => 'islam',
      'alamat' => 'jl. jalan no. 123, semarang',
      'telpon' => '08123456789',
      'jenis_kelamin' => 'l',
      'marital' => 'lajang',
      'pendidikan' => 's1',
      'bpjs_tk' => '001234567',
      'bpjs_ks' => '001234567',
      'tgl_lahir' => '1995-05-15',
      'tgl_masuk' => '2024-01-01',
      'tgl_keluar' => null,
      'tgl_penetapan' => '2024-01-01',
      'img' => 'uploads/default.webp',
      'status' => 'aktif',
    ]);
  }
}
