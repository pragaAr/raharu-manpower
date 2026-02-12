<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    // Drop foreign key and column in user table
    Schema::table('user', function (Blueprint $table) {
      $table->dropForeign(['role_id']);
      $table->dropColumn('role_id');
    });

    Schema::dropIfExists('permission_role');
    Schema::dropIfExists('permissions');
    Schema::dropIfExists('role');
  }

  public function down(): void
  {
    // Don't really need a down for this as we are migrating to Spatie
  }
};
