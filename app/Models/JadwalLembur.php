<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalLembur extends Model
{
  const TYPE_NORMAL = 'normal';
  const TYPE_HOLIDAY = 'holiday';

  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';

  protected $table = 'jadwal_lembur';

  protected $fillable = [
    'karyawan_id',
    'tanggal',
    'type',
    'jam_mulai',
    'jam_selesai',
    'status',
    'approved_by',
    'approved_at',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'tanggal'     => 'date',
    'jam_mulai'   => 'datetime:H:i',
    'jam_selesai' => 'datetime:H:i',
    'approved_at' => 'datetime',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function updater()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }

  public function perubahanRequests()
  {
    return $this->hasMany(PerubahanLemburRequest::class, 'jadwal_lembur_id', 'id');
  }

  public static function typeList(): array
  {
    return [
      self::TYPE_NORMAL,
      self::TYPE_HOLIDAY,
    ];
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
