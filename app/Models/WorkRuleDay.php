<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkRuleDay extends Model
{
  protected $table = 'work_rule_days';

  protected $fillable = [
    'work_rule_id',
    'day_of_week',
    'jam_masuk',
    'jam_pulang',
    'is_workday',
  ];

  protected $casts = [
    'day_of_week' => 'integer',
    'jam_masuk'   => 'datetime:H:i',
    'jam_pulang'  => 'datetime:H:i',
    'is_workday'  => 'boolean',
  ];

  public function workRule()
  {
    return $this->belongsTo(WorkRule::class, 'work_rule_id', 'id');
  }
}
