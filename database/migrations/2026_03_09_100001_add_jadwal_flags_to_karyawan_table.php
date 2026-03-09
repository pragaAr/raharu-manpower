<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('karyawan', function (Blueprint $table) {
      $table->boolean('ikut_jadwal_kerja')->default(true)->after('status');
      $table->boolean('ikut_jadwal_lembur')->default(false)->after('ikut_jadwal_kerja');
    });
  }

  public function down(): void
  {
    Schema::table('karyawan', function (Blueprint $table) {
      $table->dropColumn(['ikut_jadwal_kerja', 'ikut_jadwal_lembur']);
    });
  }
};
