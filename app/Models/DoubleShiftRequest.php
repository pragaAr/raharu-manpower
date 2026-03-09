<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoubleShiftRequest extends Model
{
  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';

  protected $table = 'double_shift_request';

  protected $fillable = [
    'karyawan_id',
    'tanggal',
    'shift_awal_id',
    'shift_tambahan_id',
    'catatan',
    'status',
    'approved_by',
    'approved_at',
  ];

  protected $casts = [
    'tanggal'     => 'date',
    'approved_at' => 'datetime',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function shiftAwal()
  {
    return $this->belongsTo(ShiftMaster::class, 'shift_awal_id', 'id');
  }

  public function shiftTambahan()
  {
    return $this->belongsTo(ShiftMaster::class, 'shift_tambahan_id', 'id');
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
