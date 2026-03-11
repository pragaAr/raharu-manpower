<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Jabatan;


use App\Services\Karyawan\CreateData;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
  public function run(): void
  {
    $faker = Faker::create('id_ID');
    $createDataService = app()->make(CreateData::class);

    // Fetch existing required master data
    $lokasiPusat = Lokasi::first();
    $lokasiSatpam = Lokasi::where('nama', 'like', '%satpam%')->first() ?? Lokasi::inRandomOrder()->first();

    $kategoriProbation = Kategori::where('nama', 'like', '%probation%')->first() ?? Kategori::first();
    $kategoriPKWT = Kategori::whereRaw('LOWER(nama) = ?', ['pkwt'])->first() ?? Kategori::where('nama', 'like', '%pkwt%')->whereRaw('LOWER(nama) != ?', ['pkwtt'])->first() ?? Kategori::inRandomOrder()->first();
    $kategoriPKWTT = Kategori::whereRaw('LOWER(nama) = ?', ['pkwtt'])->first() ?? Kategori::where('nama', 'like', '%pkwtt%')->first() ?? Kategori::inRandomOrder()->first();

    $jabatanDireksi = Jabatan::where('nama', 'like', '%direktur%')->orWhere('nama', 'like', '%komisaris%')->first() ?? Jabatan::first();

    $jabatanAcakIds = Jabatan::pluck('id')->toArray();

    $jabatanSatpam = Jabatan::where('nama', 'like', '%satpam%')->first() ?? Jabatan::inRandomOrder()->first();

    if (!$lokasiPusat || !$kategoriProbation || empty($jabatanAcakIds)) {
      $this->command->error("Pastikan data master (Lokasi, Kategori, Jabatan) sudah ada sebelum menjalankan KaryawanSeeder.");
      return;
    }

    // Base Data definition helper
    $getBaseData = function ($kategoriId, $lokasiId, $jabatanId) use ($faker) {
      return [
        'nama' => $faker->name,
        'kategori_id' => $kategoriId,
        'lokasi_id' => $lokasiId,
        'jabatan_id' => $jabatanId,
        'ktp' => '332' . $faker->numerify('#############'),
        'agama' => $faker->randomElement(['islam', 'kristen', 'katolik', 'hindu', 'budha', 'konghucu']),
        'alamat' => $faker->address,
        'telpon' => '08' . $faker->numerify('##########'),
        'jenis_kelamin' => $faker->randomElement(['l', 'p']),
        'marital' => $faker->randomElement(['lajang', 'menikah', 'janda', 'duda']),
        'pendidikan' => $faker->randomElement(['sma', 'd3', 's1', 's2']),
        'bpjs_tk' => '001' . $faker->numerify('#######'),
        'bpjs_ks' => '001' . $faker->numerify('#######'),
        'tgl_lahir' => $faker->date('Y-m-d', '2000-01-01'),
        'tgl_masuk' => Carbon::now()->format('Y-m-d'),
        'tgl_efektif' => Carbon::now()->format('Y-m-d'),
        'tgl_penetapan' => Carbon::now()->format('Y-m-d'),
        'tgl_mulai' => Carbon::now()->format('Y-m-d'),
        'tgl_selesai' => Carbon::now()->addYear()->format('Y-m-d'),
        'img' => 'uploads/default.webp',
        'keterangan' => 'Seeder Data',
      ];
    };

    // 1. Create 2 Karyawan Probation, jabatan null
    for ($i = 0; $i < 2; $i++) {
      $data = $getBaseData($kategoriProbation->id, $lokasiPusat->id, null);
      $createDataService->create($data);
    }

    // 2. Create 20 Karyawan PKWT, jabatan acak
    for ($i = 0; $i < 20; $i++) {
      $jabatanId = $faker->randomElement($jabatanAcakIds);
      $data = $getBaseData($kategoriPKWT->id, $lokasiPusat->id, $jabatanId);
      $createDataService->create($data);
    }

    // 3. Create 10 Karyawan PKWTT, jabatan
    for ($i = 0; $i < 10; $i++) {
      $data = $getBaseData($kategoriPKWTT->id, $lokasiPusat->id, $jabatanDireksi->id);
      $createDataService->create($data);
    }

    // 4. Create 7 Karyawan dengan jadwal satpam di lokasi yang sama
    $satpamKaryawanIds = [];
    for ($i = 0; $i < 7; $i++) {
      // These might be PKWTT or any, we use PKWTT. Target location is $lokasiSatpam
      $data = $getBaseData($kategoriPKWTT->id, $lokasiSatpam->id, $jabatanSatpam->id);
      $karyawan = $createDataService->create($data);
      $satpamKaryawanIds[] = $karyawan->id;
    }
  }
}
