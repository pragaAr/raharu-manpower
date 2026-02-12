<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
  protected $table = 'kontrak';

  protected $fillable = [
    'karyawan_id',
    'nama_karyawan',
    'kontrak_ke',
    'tgl_mulai',
    'tgl_selesai',
    'status',
  ];

  protected $casts = [
    'tgl_mulai'   => 'date',
    'tgl_selesai' => 'date',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public static function cekKontrakTerakhir($karyawanIds)
  {
    return self::whereIn('karyawan_id', $karyawanIds)
      ->selectRaw('karyawan_id, MAX(kontrak_ke) as kontrak_terakhir')
      ->groupBy('karyawan_id')
      ->pluck('kontrak_terakhir', 'karyawan_id');
  }
}
