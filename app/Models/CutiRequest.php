<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiRequest extends Model
{
  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';

  protected $table = 'cuti_request';

  protected $fillable = [
    'karyawan_id',
    'tanggal_mulai',
    'tanggal_selesai',
    'alasan',
    'status',
    'approved_by',
    'approved_at',
  ];

  protected $casts = [
    'tanggal_mulai'   => 'date',
    'tanggal_selesai' => 'date',
    'approved_at'     => 'datetime',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by', 'id');
  }
}
