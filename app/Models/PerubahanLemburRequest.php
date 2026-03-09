<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerubahanLemburRequest extends Model
{
  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';

  protected $table = 'perubahan_lembur_request';

  protected $fillable = [
    'karyawan_id',
    'jadwal_lembur_id',
    'tanggal',
    'jam_mulai_lama',
    'jam_selesai_lama',
    'jam_mulai_baru',
    'jam_selesai_baru',
    'alasan',
    'status',
    'approved_by',
    'approved_at',
  ];

  protected $casts = [
    'tanggal'          => 'date',
    'jam_mulai_lama'   => 'datetime:H:i',
    'jam_selesai_lama' => 'datetime:H:i',
    'jam_mulai_baru'   => 'datetime:H:i',
    'jam_selesai_baru' => 'datetime:H:i',
    'approved_at'      => 'datetime',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function jadwalLembur()
  {
    return $this->belongsTo(JadwalLembur::class, 'jadwal_lembur_id', 'id');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by', 'id');
  }

  public static function statusList(): array
  {
    return [
      self::STATUS_PENDING,
      self::STATUS_APPROVED,
      self::STATUS_REJECTED,
      self::STATUS_CANCELLED,
    ];
  }
}
