<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
  public function run(): void
  {
    // 1. Definisikan Permission
    $permissions = [
      // Access Control
      ['name' => 'web.access', 'label' => 'Akses Web Dashboard'],

      // Karyawan
      ['name' => 'karyawan.view', 'label' => 'Lihat Data Karyawan'],
      ['name' => 'karyawan.create', 'label' => 'Tambah Karyawan'],
      ['name' => 'karyawan.edit', 'label' => 'Ubah Karyawan'],
      ['name' => 'karyawan.delete', 'label' => 'Hapus Karyawan'],
      ['name' => 'karyawan.detail', 'label' => 'Lihat Detail Karyawan'],
      ['name' => 'karyawan.mutasi', 'label' => 'Mutasi Karyawan'],
      ['name' => 'karyawan.renewal', 'label' => 'Renewal Kontrak Karyawan'],
      ['name' => 'karyawan.change_status', 'label' => 'Ubah Status Karyawan'],

      // Kategori
      ['name' => 'kategori.view', 'label' => 'Lihat Data Kategori'],
      ['name' => 'kategori.create', 'label' => 'Tambah Kategori'],
      ['name' => 'kategori.edit', 'label' => 'Ubah Kategori'],
      ['name' => 'kategori.delete', 'label' => 'Hapus Kategori'],

      // Lokasi
      ['name' => 'lokasi.view', 'label' => 'Lihat Data Lokasi'],
      ['name' => 'lokasi.create', 'label' => 'Tambah Lokasi'],
      ['name' => 'lokasi.edit', 'label' => 'Ubah Lokasi'],
      ['name' => 'lokasi.delete', 'label' => 'Hapus Lokasi'],

      // Divisi
      ['name' => 'divisi.view', 'label' => 'Lihat Data Divisi'],
      ['name' => 'divisi.create', 'label' => 'Tambah Divisi'],
      ['name' => 'divisi.edit', 'label' => 'Ubah Divisi'],
      ['name' => 'divisi.delete', 'label' => 'Hapus Divisi'],

      // Unit
      ['name' => 'unit.view', 'label' => 'Lihat Data Unit'],
      ['name' => 'unit.create', 'label' => 'Tambah Unit'],
      ['name' => 'unit.edit', 'label' => 'Ubah Unit'],
      ['name' => 'unit.delete', 'label' => 'Hapus Unit'],

      // Jabatan
      ['name' => 'jabatan.view', 'label' => 'Lihat Data Jabatan'],
      ['name' => 'jabatan.create', 'label' => 'Tambah Jabatan'],
      ['name' => 'jabatan.edit', 'label' => 'Ubah Jabatan'],
      ['name' => 'jabatan.delete', 'label' => 'Hapus Jabatan'],

      // Absensi
      ['name' => 'absensi.view', 'label' => 'Lihat Data Absensi'],
      ['name' => 'absensi.create', 'label' => 'Tambah Absensi'],
      ['name' => 'absensi.edit', 'label' => 'Ubah Absensi'],
      ['name' => 'absensi.delete', 'label' => 'Hapus Absensi'],
      ['name' => 'absensi.detail', 'label' => 'Lihat Detail Absensi'],

      // User Management
      ['name' => 'user.view', 'label' => 'Lihat Data User'],
      ['name' => 'user.create', 'label' => 'Tambah User'],
      ['name' => 'user.edit', 'label' => 'Ubah User'],
      ['name' => 'user.delete', 'label' => 'Hapus User'],

      // Role Management
      ['name' => 'role.view', 'label' => 'Lihat Data Role'],
      ['name' => 'role.create', 'label' => 'Tambah Role'],
      ['name' => 'role.edit', 'label' => 'Ubah Role'],
      ['name' => 'role.delete', 'label' => 'Hapus Role'],

      // Permission Management
      ['name' => 'permission.view', 'label' => 'Lihat Data Permission'],
      ['name' => 'permission.create', 'label' => 'Tambah Permission'],
      ['name' => 'permission.edit', 'label' => 'Ubah Permission'],
      ['name' => 'permission.delete', 'label' => 'Hapus Permission'],
    ];

    foreach ($permissions as $p) {
      Permission::updateOrCreate(['name' => $p['name']], $p);
    }

    // 2. Buat Role
    $superuserRole = Role::updateOrCreate(['name' => 'Superuser']);
    $adminRole = Role::updateOrCreate(['name' => 'Administrator']);
    $staffRole = Role::updateOrCreate(['name' => 'Staff']);
    $karyawanRole = Role::updateOrCreate(['name' => 'Karyawan']);

    // 3. Assign Permission ke Role
    // Admin dapat semua permission
    $adminRole->syncPermissions(Permission::all());

    // Staff hanya dapat view
    $staffRole->syncPermissions([
      'web.access',
      'karyawan.view',
      'karyawan.detail',
      'kategori.view',
      'lokasi.view',
      'divisi.view',
      'unit.view',
      'jabatan.view',
      'absensi.view',
    ]);
  }
}
