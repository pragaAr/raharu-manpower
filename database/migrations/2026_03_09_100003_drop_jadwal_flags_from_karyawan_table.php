<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    $columns = [];

    if (Schema::hasColumn('karyawan', 'ikut_jadwal_kerja')) {
      $columns[] = 'ikut_jadwal_kerja';
    }

    if (Schema::hasColumn('karyawan', 'ikut_jadwal_lembur')) {
      $columns[] = 'ikut_jadwal_lembur';
    }

    if (!empty($columns)) {
      Schema::table('karyawan', function (Blueprint $table) use ($columns) {
        $table->dropColumn($columns);
      });
    }
  }

  public function down(): void
  {
    Schema::table('karyawan', function (Blueprint $table) {
      if (!Schema::hasColumn('karyawan', 'ikut_jadwal_kerja')) {
        $table->boolean('ikut_jadwal_kerja')->default(true)->after('status');
      }

      if (!Schema::hasColumn('karyawan', 'ikut_jadwal_lembur')) {
        $table->boolean('ikut_jadwal_lembur')->default(false)->after('ikut_jadwal_kerja');
      }
    });
  }
};
