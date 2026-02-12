<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('kontrak', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->nullable()
        ->constrained('karyawan')
        ->nullOnDelete();
      $table->string('nama_karyawan', 50);
      $table->integer('kontrak_ke');
      $table->date('tgl_mulai');
      $table->date('tgl_selesai');
      $table->string('status', 20);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('kontrak');
  }
};
