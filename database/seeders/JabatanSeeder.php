<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
  public function run(): void
  {
    $data = [
      ['unit_id' => 1, 'nama' => 'direktur'],
      ['unit_id' => 1, 'nama' => 'komisaris'],
      ['unit_id' => 1, 'nama' => 'manager'],
      ['unit_id' => 1, 'nama' => 'staff'],
      ['unit_id' => 8, 'nama' => 'programmer'],
      ['unit_id' => 8, 'nama' => 'staff'],
      ['unit_id' => 6, 'nama' => 'supervisor'],
      ['unit_id' => 6, 'nama' => 'mandor paket'],
      ['unit_id' => 6, 'nama' => 'mandor loper'],
      ['unit_id' => 6, 'nama' => 'bongkar muat'],
      ['unit_id' => 6, 'nama' => 'sopir loper'],
      ['unit_id' => 6, 'nama' => 'loper'],
      ['unit_id' => 6, 'nama' => 'customer service'],
      ['unit_id' => 6, 'nama' => 'staff'],
      ['unit_id' => 2, 'nama' => 'staff'],
      ['unit_id' => 10, 'nama' => 'staff'],
    ];

    foreach ($data as $item) {
      Jabatan::create($item);
    }
  }
}
