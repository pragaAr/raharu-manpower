<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
  protected $table = 'divisi';

  protected $fillable = [
    'nama',
    'kode',
  ];

  public function units()
  {
    return $this->hasMany(Unit::class, 'divisi_id', 'id');
  }

  public function jabatans()
  {
    return $this->hasMany(Jabatan::class, 'divisi_id', 'id');
  }
}
