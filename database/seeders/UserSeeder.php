<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    // Pastikan role sudah ada (sudah dijalankan di RolePermissionSeeder)
    $superuserRole = Role::where('name', 'Superuser')->first();

    if ($superuserRole) {
      $user = User::updateOrCreate(
        ['username' => 'admin'],
        [
          'karyawan_id' => null, // Superuser = entitas sistem, tidak terikat karyawan
          'password' => Hash::make('password'),
        ]
      );

      $user->syncRoles([$superuserRole]);
    }
  }
}
