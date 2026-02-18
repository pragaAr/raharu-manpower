<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
  use HasFactory;

  const STATUS_HADIR  = 'hadir';
  const STATUS_IZIN   = 'izin';
  const STATUS_SAKIT  = 'sakit';
  const STATUS_ALPHA  = 'alpha';
  const STATUS_CUTI   = 'cuti';

  protected $table = 'absensi';

  protected $fillable = [
    'karyawan_id',
    'tanggal',
    'status',
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

  public static function statusList(): array
  {
    return [
      self::STATUS_HADIR,
      self::STATUS_IZIN,
      self::STATUS_SAKIT,
      self::STATUS_ALPHA,
      self::STATUS_CUTI,
    ];
  }
}
