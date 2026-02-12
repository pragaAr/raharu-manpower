<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
  protected $table = 'lokasi';

  protected $fillable = [
    'nama',
    'kode',
    'lat',
    'lng'
  ];

  public function karyawans()
  {
    return $this->hasMany(Karyawan::class, 'lokasi_id', 'id');
  }
}
