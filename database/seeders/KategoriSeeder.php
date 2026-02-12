<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
  public function run(): void
  {
    $data = [
      ['nama' => 'pkwtt', 'keterangan' => 'perjanjian kerja waktu tidak tertentu (tetap)'],
      ['nama' => 'pkwt', 'keterangan' => 'perjanjian kerja waktu tertentu (kontrak)'],
      ['nama' => 'magang', 'keterangan' => 'program magang/internship'],
      ['nama' => 'outsourcing', 'keterangan' => 'karyawan outsourcing/alih daya'],
      ['nama' => 'harian', 'keterangan' => 'pekerja lepas'],
      ['nama' => 'probation', 'keterangan' => 'masa percobaan evaluasi kinerja'],
    ];

    foreach ($data as $item) {
      Kategori::create($item);
    }
  }
}
