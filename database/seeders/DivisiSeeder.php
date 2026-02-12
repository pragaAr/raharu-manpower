<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
  public function run(): void
  {

    $data = [
      ['nama' => 'direktorat', 'kode' => 'a'],
      ['nama' => 'keuangan', 'kode' => 'b'],
      ['nama' => 'operasional', 'kode' => 'c'],
      ['nama' => 'it', 'kode' => 'd'],
      ['nama' => 'hrd-ga', 'kode' => 'e'],
      ['nama' => 'internal audit', 'kode' => 'f'],
    ];

    foreach ($data as $item) {
      Divisi::create($item);
    }
  }
}
