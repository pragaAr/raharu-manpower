<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lokasi;

class LokasiSeeder extends Seeder
{
  public function run(): void
  {
    $data = [
      ['nama' => 'pusat', 'kode' => 'a', 'lat' => -6.958700, 'lng' => 110.471300],
      ['nama' => 'jakarta barat', 'kode' => 'b', 'lat' => -6.168329, 'lng' => 106.758849],
      ['nama' => 'surabaya', 'kode' => 'c', 'lat' => -7.257472, 'lng' => 112.752090],
      ['nama' => 'bandung', 'kode' => 'd', 'lat' => -6.917464, 'lng' => 107.619123],
      ['nama' => 'semarang', 'kode' => 'e', 'lat' => -6.966667, 'lng' => 110.416664],
      ['nama' => 'yogyakarta', 'kode' => 'f', 'lat' => -7.795580, 'lng' => 110.369490],
      ['nama' => 'solo', 'kode' => 'g', 'lat' => -7.566600, 'lng' => 110.816700],
      ['nama' => 'tegal', 'kode' => 'h', 'lat' => -6.869400, 'lng' => 109.140200],
      ['nama' => 'malang', 'kode' => 'i', 'lat' => -7.966620, 'lng' => 112.632629],
      ['nama' => 'cirebon', 'kode' => 'j', 'lat' => -6.732023, 'lng' => 108.552316],
      ['nama' => 'jakarta timur', 'kode' => 'k', 'lat' => -6.225014, 'lng' => 106.900447],
      ['nama' => 'denpasar', 'kode' => 'l', 'lat' => -8.670458, 'lng' => 115.212629],
    ];

    // foreach ($data as $item) {
    //   Lokasi::updateOrCreate(
    //     ['kode' => $item['kode']],
    //     $item
    //   );
    // }

    foreach ($data as $item) {
      Lokasi::create($item);
    }
  }
}
