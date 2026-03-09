<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    // 1. work_rule — aturan kerja per jabatan
    Schema::create('work_rule', function (Blueprint $table) {
      $table->id();
      $table->foreignId('jabatan_id')
        ->constrained('jabatan')
        ->restrictOnDelete();
      $table->boolean('use_shift')->default(false);
      $table->boolean('auto_overtime')->default(false);
      $table->boolean('overtime_need_approval')->default(true);
      $table->boolean('cuti_need_approval')->default(true);
      $table->boolean('allow_double_shift')->default(false);
      $table->boolean('allow_shift_swap')->default(false);
      $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamps();

      $table->unique('jabatan_id');
    });

    // 2. work_rule_days — jam kerja per hari
    Schema::create('work_rule_days', function (Blueprint $table) {
      $table->id();
      $table->foreignId('work_rule_id')
        ->constrained('work_rule')
        ->cascadeOnDelete();
      $table->unsignedTinyInteger('day_of_week')->comment('1=senin, 7=minggu');
      $table->time('jam_masuk')->nullable();
      $table->time('jam_pulang')->nullable();
      $table->boolean('is_workday')->default(true);
      $table->timestamps();

      $table->unique(['work_rule_id', 'day_of_week']);
    });

    // 3. shift_master — master data shift
    Schema::create('shift_master', function (Blueprint $table) {
      $table->id();
      $table->string('nama', 50);
      $table->time('jam_masuk');
      $table->time('jam_pulang');
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    // 4. jadwal_karyawan — jadwal harian per karyawan
    Schema::create('jadwal_karyawan', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->foreignId('shift_id')
        ->nullable()
        ->constrained('shift_master')
        ->nullOnDelete();
      $table->string('shift_nama', 50)->nullable();
      $table->time('jam_masuk')->nullable();
      $table->time('jam_pulang')->nullable();
      $table->boolean('is_libur')->default(false);
      $table->boolean('is_holiday')->default(false);
      $table->string('generated_by', 30)->nullable()->comment('system atau null');
      $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamps();

      $table->unique(['karyawan_id', 'tanggal']);
    });

    // 5. jadwal_lembur — record lembur + status approval
    Schema::create('jadwal_lembur', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->enum('type', ['normal', 'holiday'])->default('normal');
      $table->time('jam_mulai');
      $table->time('jam_selesai');
      $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
      $table->foreignId('approved_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamps();

      $table->index(['karyawan_id', 'tanggal']);
    });

    // 6. holiday — hari libur nasional
    Schema::create('holiday', function (Blueprint $table) {
      $table->id();
      $table->date('tanggal');
      $table->string('nama', 100);
      $table->boolean('is_national')->default(true);
      $table->timestamps();

      $table->unique('tanggal');
    });

    // 7. cuti_request — pengajuan cuti
    Schema::create('cuti_request', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal_mulai');
      $table->date('tanggal_selesai');
      $table->string('alasan', 255)->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
      $table->foreignId('approved_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });

    // 8. tukar_shift_request — tukar shift antar karyawan
    Schema::create('tukar_shift_request', function (Blueprint $table) {
      $table->id();
      $table->foreignId('requester_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->foreignId('target_karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->foreignId('requester_jadwal_id')
        ->constrained('jadwal_karyawan')
        ->cascadeOnDelete();
      $table->foreignId('target_jadwal_id')
        ->constrained('jadwal_karyawan')
        ->cascadeOnDelete();
      $table->string('catatan', 255)->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
      $table->foreignId('approved_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });

    // 9. perubahan_lembur_request — request ubah jam lembur
    Schema::create('perubahan_lembur_request', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->foreignId('jadwal_lembur_id')
        ->constrained('jadwal_lembur')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->time('jam_mulai_lama');
      $table->time('jam_selesai_lama');
      $table->time('jam_mulai_baru');
      $table->time('jam_selesai_baru');
      $table->string('alasan', 255)->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
      $table->foreignId('approved_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });

    // 10. double_shift_request — ambil shift tambahan
    Schema::create('double_shift_request', function (Blueprint $table) {
      $table->id();
      $table->foreignId('karyawan_id')
        ->constrained('karyawan')
        ->cascadeOnDelete();
      $table->date('tanggal');
      $table->foreignId('shift_awal_id')
        ->constrained('shift_master')
        ->restrictOnDelete();
      $table->foreignId('shift_tambahan_id')
        ->constrained('shift_master')
        ->restrictOnDelete();
      $table->string('catatan', 255)->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
      $table->foreignId('approved_by')->nullable()->constrained('user')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });

    // 11. request_log — audit trail semua request
    Schema::create('request_log', function (Blueprint $table) {
      $table->id();
      $table->enum('request_type', ['cuti', 'tukar_shift', 'perubahan_lembur', 'double_shift']);
      $table->unsignedBigInteger('request_id');
      $table->enum('action', ['created', 'approved', 'rejected', 'cancelled', 'revised']);
      $table->foreignId('actor_id')
        ->constrained('user')
        ->restrictOnDelete();
      $table->string('catatan', 255)->nullable();
      $table->timestamp('created_at')->nullable();

      $table->index(['request_type', 'request_id']);
    });

    // 12. rencana_lembur_harian — jadwal lembur terencana (opsional)
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

  public function down(): void
  {
    Schema::dropIfExists('rencana_lembur_harian');
    Schema::dropIfExists('request_log');
    Schema::dropIfExists('double_shift_request');
    Schema::dropIfExists('perubahan_lembur_request');
    Schema::dropIfExists('tukar_shift_request');
    Schema::dropIfExists('cuti_request');
    Schema::dropIfExists('holiday');
    Schema::dropIfExists('jadwal_lembur');
    Schema::dropIfExists('jadwal_karyawan');
    Schema::dropIfExists('shift_master');
    Schema::dropIfExists('work_rule_days');
    Schema::dropIfExists('work_rule');
  }
};
