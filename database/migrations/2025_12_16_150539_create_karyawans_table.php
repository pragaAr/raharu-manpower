<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('karyawan', function (Blueprint $table) {
      $table->id();
      $table->foreignId('kategori_id')
        ->constrained('kategori')
        ->restrictOnDelete();
      $table->foreignId('lokasi_id')
        ->constrained('lokasi')
        ->restrictOnDelete();
      $table->foreignId('jabatan_id')
        ->nullable()
        ->constrained('jabatan')
        ->restrictOnDelete();
      $table->string('nik', 30)->unique();
      $table->string('nama', 50);
      $table->string('ktp', 20)->unique();
      $table->string('agama', 15);
      $table->string('alamat', 255);
      $table->string('telpon', 20);
      $table->string('jenis_kelamin', 20);
      $table->string('marital', 25);
      $table->string('pendidikan', 25);
      $table->string('bpjs_tk', 25)->nullable();
      $table->string('bpjs_ks', 25)->nullable();
      $table->date('tgl_lahir');
      $table->date('tgl_masuk');
      $table->date('tgl_keluar')->nullable();
      $table->date('tgl_penetapan')->nullable();
      $table->string('img', 255);
      $table->string('status', 20);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('karyawan');
  }
};
