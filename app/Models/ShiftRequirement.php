<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftRequirement extends Model
{
  protected $table = 'shift_requirement';

  protected $fillable = [
    'lokasi_id',
    'shift_id',
    'required_count',
  ];

  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
  }

  public function shift()
  {
    return $this->belongsTo(ShiftMaster::class, 'shift_id', 'id');
  }
}
