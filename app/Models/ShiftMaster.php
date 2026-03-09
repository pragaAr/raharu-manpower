<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftMaster extends Model
{
  protected $table = 'shift_master';

  protected $fillable = [
    'nama',
    'jam_masuk',
    'jam_pulang',
    'is_active',
  ];

  protected $casts = [
    'jam_masuk' => 'datetime:H:i',
    'jam_pulang' => 'datetime:H:i',
    'is_active' => 'boolean',
  ];

  public function jadwalKaryawans()
  {
    return $this->hasMany(JadwalKaryawan::class, 'shift_id', 'id');
  }

  public function doubleShiftAwals()
  {
    return $this->hasMany(DoubleShiftRequest::class, 'shift_awal_id', 'id');
  }

  public function doubleShiftTambahans()
  {
    return $this->hasMany(DoubleShiftRequest::class, 'shift_tambahan_id', 'id');
  }
}
