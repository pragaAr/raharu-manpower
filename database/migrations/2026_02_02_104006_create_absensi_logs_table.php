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
    Schema::create('absensi_logs', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('absensi_id');

      $table->enum('jenis', ['masuk', 'pulang']);

      $table->time('jam');

      $table->enum('source', ['mobile', 'manual']);

      $table->foreignId('input_by')
        ->nullable()
        ->constrained('user');

      $table->text('keterangan')->nullable();

      $table->timestamps();

      $table->index(['absensi_id', 'jenis']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('absensi_logs');
  }
};
