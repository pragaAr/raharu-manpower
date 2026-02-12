<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('history', function (Blueprint $table) {
      $table->id();
      $table->string('jenis', 25);
      $table->foreignId('karyawan_id')
        ->nullable()
        ->constrained('karyawan')
        ->nullOnDelete();
      $table->string('nama_karyawan', 50);
      $table->string('kategori_nama', 25)->nullable();
      $table->string('unit_nama', 25)->nullable();
      $table->string('divisi_nama', 25)->nullable();
      $table->string('jabatan_nama', 25)->nullable();
      $table->string('lokasi_nama', 25)->nullable();
      $table->string('nik', 30)->nullable();
      $table->string('telpon', 20)->nullable();
      $table->date('tgl_masuk')->nullable();
      $table->date('tgl_keluar')->nullable();
      $table->date('tgl_mulai')->nullable();
      $table->date('tgl_selesai')->nullable();
      $table->date('tgl_penetapan')->nullable();
      $table->string('status', 20)->nullable();
      $table->string('keterangan', 255);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('history');
  }
};
