<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
  protected $table = 'holiday';

  protected $fillable = [
    'tanggal',
    'nama',
    'is_national',
  ];

  protected $casts = [
    'tanggal'     => 'date',
    'is_national' => 'boolean',
  ];
}
