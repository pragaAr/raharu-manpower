<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
  use HasFactory;

  protected $table = 'absensi';

  protected $fillable = [
    'karyawan_id',
    'tanggal',
    'jam_masuk',
    'jam_pulang',
  ];

  protected $casts = [
    'tanggal'     => 'date',
    'jam_masuk'   => 'datetime:H:i',
    'jam_pulang'  => 'datetime:H:i',
  ];

  public function logs()
  {
    return $this->hasMany(AbsensiLog::class);
  }

  public function lastLog()
  {
    return $this->hasOne(AbsensiLog::class)->latestOfMany();
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class);
  }
}
