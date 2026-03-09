<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
  protected $table = 'jadwal_karyawan';

  protected $fillable = [
    'karyawan_id',
    'tanggal',
    'shift_id',
    'shift_nama',
    'jam_masuk',
    'jam_pulang',
    'is_libur',
    'is_holiday',
    'generated_by',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'tanggal'    => 'date',
    'jam_masuk'  => 'datetime:H:i',
    'jam_pulang' => 'datetime:H:i',
    'is_libur'   => 'boolean',
    'is_holiday' => 'boolean',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function shift()
  {
    return $this->belongsTo(ShiftMaster::class, 'shift_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function updater()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }

  public function swapRequestsAsRequester()
  {
    return $this->hasMany(TukarShiftRequest::class, 'requester_jadwal_id', 'id');
  }

  public function swapRequestsAsTarget()
  {
    return $this->hasMany(TukarShiftRequest::class, 'target_jadwal_id', 'id');
  }
}
