<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
  public function run(): void
  {
    $data = [
      ['divisi_id' => 1, 'nama' => 'managerial', 'kode' => 'a'],
      ['divisi_id' => 2, 'nama' => 'akc', 'kode' => 'b'],
      ['divisi_id' => 2, 'nama' => 'administrasi', 'kode' => 'c'],
      ['divisi_id' => 2, 'nama' => 'perpajakan', 'kode' => 'd'],
      ['divisi_id' => 3, 'nama' => 'marketing', 'kode' => 'e'],
      ['divisi_id' => 3, 'nama' => 'paket', 'kode' => 'f'],
      ['divisi_id' => 3, 'nama' => 'bengkel', 'kode' => 'g'],
      ['divisi_id' => 4, 'nama' => 'software', 'kode' => 'h'],
      ['divisi_id' => 4, 'nama' => 'hardware', 'kode' => 'i'],
      ['divisi_id' => 5, 'nama' => 'hrd', 'kode' => 'j'],
      ['divisi_id' => 5, 'nama' => 'keamanan', 'kode' => 'k'],
      ['divisi_id' => 5, 'nama' => 'ga', 'kode' => 'l'],
      ['divisi_id' => 6, 'nama' => 'audit', 'kode' => 'm'],
    ];

    foreach ($data as $item) {
      Unit::create($item);
    }
  }
}
