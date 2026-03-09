<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftMasterSeeder extends Seeder
{
  public function run(): void
  {
    $shifts = [
      [
        'nama'       => 'Pagi',
        'jam_masuk'  => '06:00:00',
        'jam_pulang' => '14:00:00',
        'is_active'  => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'nama'       => 'Sore',
        'jam_masuk'  => '14:00:00',
        'jam_pulang' => '22:00:00',
        'is_active'  => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'nama'       => 'Malam',
        'jam_masuk'  => '22:00:00',
        'jam_pulang' => '06:00:00',
        'is_active'  => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ];

    foreach ($shifts as $shift) {
      DB::table('shift_master')->updateOrInsert(
        ['nama' => $shift['nama']],
        $shift
      );
    }
  }
}
