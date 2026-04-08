<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('shift_requirement', function (Blueprint $table) {
      $table->id();
      $table->foreignId('lokasi_id')
        ->constrained('lokasi')
        ->cascadeOnDelete();
      $table->foreignId('shift_id')
        ->constrained('shift_master')
        ->cascadeOnDelete();
      $table->unsignedInteger('required_count')->default(0);
      $table->timestamps();

      $table->unique(['lokasi_id', 'shift_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('shift_requirement');
  }
};
