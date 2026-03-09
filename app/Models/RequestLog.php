<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
  const UPDATED_AT = null;

  protected $table = 'request_log';

  protected $fillable = [
    'request_type',
    'request_id',
    'action',
    'actor_id',
    'catatan',
    'created_at',
  ];

  protected $casts = [
    'request_id'  => 'integer',
    'created_at'  => 'datetime',
  ];

  public function actor()
  {
    return $this->belongsTo(User::class, 'actor_id', 'id');
  }
}
