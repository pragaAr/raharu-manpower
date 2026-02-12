<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
  protected $table = 'unit';

  protected $fillable = [
    'divisi_id',
    'nama',
    'kode'
  ];

  public function divisi()
  {
    return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
  }
}
