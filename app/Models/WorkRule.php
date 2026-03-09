<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkRule extends Model
{
  protected $table = 'work_rule';

  protected $fillable = [
    'jabatan_id',
    'use_shift',
    'auto_overtime',
    'overtime_need_approval',
    'cuti_need_approval',
    'allow_double_shift',
    'allow_shift_swap',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'use_shift'              => 'boolean',
    'auto_overtime'          => 'boolean',
    'overtime_need_approval' => 'boolean',
    'cuti_need_approval'     => 'boolean',
    'allow_double_shift'     => 'boolean',
    'allow_shift_swap'       => 'boolean',
  ];

  public function jabatan()
  {
    return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
  }

  public function days()
  {
    return $this->hasMany(WorkRuleDay::class, 'work_rule_id', 'id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  public function updater()
  {
    return $this->belongsTo(User::class, 'updated_by', 'id');
  }
}
