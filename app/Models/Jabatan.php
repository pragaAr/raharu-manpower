<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
  protected $table = 'jabatan';

  protected $fillable = [
    'unit_id',
    'nama',
  ];

  public function unit()
  {
    return $this->belongsTo(Unit::class, 'unit_id', 'id');
  }

  public function karyawans()
  {
    return $this->hasMany(Karyawan::class, 'jabatan_id', 'id');
  }
}
