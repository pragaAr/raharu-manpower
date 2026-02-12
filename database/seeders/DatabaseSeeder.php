<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      DivisiSeeder::class,
      UnitSeeder::class,
      LokasiSeeder::class,
      KategoriSeeder::class,
      JabatanSeeder::class,
      KaryawanSeeder::class,
      RolePermissionSeeder::class,
      UserSeeder::class,
    ]);
  }
}
