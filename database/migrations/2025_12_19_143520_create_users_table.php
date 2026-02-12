<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->nullable()
        ->constrained('karyawan')
        ->nullOnDelete();
      $table->foreignId('role_id')
        ->constrained('role')
        ->restrictOnDelete();
      $table->string('username')->unique();
      $table->string('password');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('user');
  }
};
