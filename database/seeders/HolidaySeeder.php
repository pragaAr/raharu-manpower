<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
  public function run(): void
  {
    // Hari libur nasional Indonesia 2026
    $holidays = [
      ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru Masehi', 'is_national' => true],
      ['tanggal' => '2026-01-29', 'nama' => 'Tahun Baru Imlek', 'is_national' => true],
      ['tanggal' => '2026-02-17', 'nama' => 'Isra Miraj Nabi Muhammad SAW', 'is_national' => true],
      ['tanggal' => '2026-03-22', 'nama' => 'Hari Raya Nyepi', 'is_national' => true],
      ['tanggal' => '2026-03-29', 'nama' => 'Wafat Isa Almasih', 'is_national' => true],
      ['tanggal' => '2026-03-30', 'nama' => 'Hari Raya Idul Fitri 1447H (Hari 1)', 'is_national' => true],
      ['tanggal' => '2026-03-31', 'nama' => 'Hari Raya Idul Fitri 1447H (Hari 2)', 'is_national' => true],
      ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional', 'is_national' => true],
      ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Isa Almasih', 'is_national' => true],
      ['tanggal' => '2026-05-26', 'nama' => 'Hari Raya Waisak', 'is_national' => true],
      ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila', 'is_national' => true],
      ['tanggal' => '2026-06-06', 'nama' => 'Hari Raya Idul Adha 1447H', 'is_national' => true],
      ['tanggal' => '2026-06-27', 'nama' => 'Tahun Baru Islam 1448H', 'is_national' => true],
      ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan RI', 'is_national' => true],
      ['tanggal' => '2026-09-05', 'nama' => 'Maulid Nabi Muhammad SAW', 'is_national' => true],
      ['tanggal' => '2026-12-25', 'nama' => 'Hari Raya Natal', 'is_national' => true],
    ];

    foreach ($holidays as $holiday) {
      DB::table('holiday')->updateOrInsert(
        ['tanggal' => $holiday['tanggal']],
        array_merge($holiday, [
          'created_at' => now(),
          'updated_at' => now(),
        ])
      );
    }
  }
}
