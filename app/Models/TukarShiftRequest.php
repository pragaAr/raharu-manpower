<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TukarShiftRequest extends Model
{
  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';

  protected $table = 'tukar_shift_request';

  protected $fillable = [
    'requester_id',
    'target_karyawan_id',
    'tanggal',
    'requester_jadwal_id',
    'target_jadwal_id',
    'catatan',
    'status',
    'approved_by',
    'approved_at',
  ];

  protected $casts = [
    'tanggal'     => 'date',
    'approved_at' => 'datetime',
  ];

  public function requester()
  {
    return $this->belongsTo(Karyawan::class, 'requester_id', 'id');
  }

  public function targetKaryawan()
  {
    return $this->belongsTo(Karyawan::class, 'target_karyawan_id', 'id');
  }

  public function requesterJadwal()
  {
    return $this->belongsTo(JadwalKaryawan::class, 'requester_jadwal_id', 'id');
  }

  public function targetJadwal()
  {
    return $this->belongsTo(JadwalKaryawan::class, 'target_jadwal_id', 'id');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by', 'id');
  }
}
