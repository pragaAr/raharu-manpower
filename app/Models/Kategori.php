<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
  protected $table = 'kategori';

  protected $fillable = [
    'nama',
    'keterangan'
  ];

  public function karyawans()
  {
    return $this->hasMany(Karyawan::class, 'kategori_id', 'id');
  }
}
