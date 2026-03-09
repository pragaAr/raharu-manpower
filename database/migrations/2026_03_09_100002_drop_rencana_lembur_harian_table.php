<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::dropIfExists('rencana_lembur_harian');
  }

  public function down(): void
  {
    Schema::create('rencana_lembur_harian', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->time('jam_mulai_rencana');
      $table->time('jam_selesai_rencana');
      $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamps();

      $table->unique(['karyawan_id', 'tanggal']);
    });
  }
};
