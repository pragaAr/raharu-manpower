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

      // Shift
      ['name' => 'shift.view', 'label' => 'Lihat Data Shift'],
      ['name' => 'shift.create', 'label' => 'Tambah Shift'],
      ['name' => 'shift.edit', 'label' => 'Ubah Shift'],
      ['name' => 'shift.delete', 'label' => 'Hapus Shift'],

      // Holiday
      ['name' => 'holiday.view', 'label' => 'Lihat Data Hari Libur'],
      ['name' => 'holiday.create', 'label' => 'Tambah Hari Libur'],
      ['name' => 'holiday.edit', 'label' => 'Ubah Hari Libur'],
      ['name' => 'holiday.delete', 'label' => 'Hapus Hari Libur'],

      // Work Rule
      ['name' => 'work-rule.view', 'label' => 'Lihat Data Aturan Kerja'],
      ['name' => 'work-rule.create', 'label' => 'Tambah Aturan Kerja'],
      ['name' => 'work-rule.edit', 'label' => 'Ubah Aturan Kerja'],
      ['name' => 'work-rule.delete', 'label' => 'Hapus Aturan Kerja'],

      // Jadwal Kerja
      ['name' => 'jadwal-kerja.view', 'label' => 'Lihat Data Jadwal Kerja'],
      ['name' => 'jadwal-kerja.create', 'label' => 'Tambah Jadwal Kerja'],
      ['name' => 'jadwal-kerja.edit', 'label' => 'Ubah Jadwal Kerja'],
      ['name' => 'jadwal-kerja.delete', 'label' => 'Hapus Jadwal Kerja'],

      // Jadwal Lembur
      ['name' => 'jadwal-lembur.view', 'label' => 'Lihat Data Jadwal Lembur'],
      ['name' => 'jadwal-lembur.create', 'label' => 'Tambah Jadwal Lembur'],
      ['name' => 'jadwal-lembur.edit', 'label' => 'Ubah Jadwal Lembur'],
      ['name' => 'jadwal-lembur.delete', 'label' => 'Hapus Jadwal Lembur'],

      // Absensi
      ['name' => 'absensi.view', 'label' => 'Lihat Data Absensi'],
      ['name' => 'absensi.create', 'label' => 'Tambah Absensi'],
      ['name' => 'absensi.edit', 'label' => 'Ubah Absensi'],
      ['name' => 'absensi.delete', 'label' => 'Hapus Absensi'],
      ['name' => 'absensi.detail', 'label' => 'Lihat Detail Absensi'],

      // Pengajuan Cuti
      ['name' => 'pengajuan-cuti.view', 'label' => 'Lihat Pengajuan Cuti'],
      ['name' => 'pengajuan-cuti.create', 'label' => 'Tambah Pengajuan Cuti'],
      ['name' => 'pengajuan-cuti.edit', 'label' => 'Ubah Pengajuan Cuti'],
      ['name' => 'pengajuan-cuti.delete', 'label' => 'Hapus Pengajuan Cuti'],

      // Pengajuan Tukar Shift
      ['name' => 'pengajuan-tukar-shift.view', 'label' => 'Lihat Pengajuan Tukar Shift'],
      ['name' => 'pengajuan-tukar-shift.create', 'label' => 'Tambah Pengajuan Tukar Shift'],
      ['name' => 'pengajuan-tukar-shift.edit', 'label' => 'Ubah Pengajuan Tukar Shift'],
      ['name' => 'pengajuan-tukar-shift.delete', 'label' => 'Hapus Pengajuan Tukar Shift'],

      // Pengajuan Perubahan Lembur
      ['name' => 'pengajuan-lembur.view', 'label' => 'Lihat Pengajuan Perubahan Lembur'],
      ['name' => 'pengajuan-lembur.create', 'label' => 'Tambah Pengajuan Perubahan Lembur'],
      ['name' => 'pengajuan-lembur.edit', 'label' => 'Ubah Pengajuan Perubahan Lembur'],
      ['name' => 'pengajuan-lembur.delete', 'label' => 'Hapus Pengajuan Perubahan Lembur'],

      // Pengajuan Double Shift
      ['name' => 'pengajuan-double-shift.view', 'label' => 'Lihat Pengajuan Double Shift'],
      ['name' => 'pengajuan-double-shift.create', 'label' => 'Tambah Pengajuan Double Shift'],
      ['name' => 'pengajuan-double-shift.edit', 'label' => 'Ubah Pengajuan Double Shift'],
      ['name' => 'pengajuan-double-shift.delete', 'label' => 'Hapus Pengajuan Double Shift'],

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
      'shift.view',
      'holiday.view',
      'work-rule.view',
      'jadwal-kerja.view',
      'jadwal-lembur.view',
      'absensi.view',
      'pengajuan-cuti.view',
      'pengajuan-tukar-shift.view',
      'pengajuan-lembur.view',
      'pengajuan-double-shift.view',
    ]);
  }
}
