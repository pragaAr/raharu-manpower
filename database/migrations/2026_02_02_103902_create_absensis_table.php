<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('absensi', function (Blueprint $table) {
      $table->id();

      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();

      $table->date('tanggal');

      $table->time('jam_masuk')->nullable();
      $table->time('jam_pulang')->nullable();

      $table->timestamps();

      $table->unique(['karyawan_id', 'tanggal']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('absensi');
  }
};
