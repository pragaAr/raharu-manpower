<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiLog extends Model
{
  use HasFactory;

  protected $table = 'absensi_logs';

  protected $fillable = [
    'absensi_id',
    'jenis',
    'jam',
    'source',
    'input_by',
    'keterangan',
  ];

  protected $casts = [
    'jam' => 'datetime:H:i'
  ];

  public function absensi()
  {
    return $this->belongsTo(Absensi::class);
  }

  public function inputBy()
  {
    return $this->belongsTo(User::class, 'input_by');
  }
}
