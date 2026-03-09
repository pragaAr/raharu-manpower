<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles;

  protected $table = 'user';

  protected $fillable = [
    'karyawan_id',
    'username',
    'password',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }

  public function absensiInputs()
  {
    return $this->hasMany(Absensi::class, 'input_by');
  }

  public function approvedJadwalLemburs()
  {
    return $this->hasMany(JadwalLembur::class, 'approved_by', 'id');
  }

  public function approvedCutiRequests()
  {
    return $this->hasMany(CutiRequest::class, 'approved_by', 'id');
  }

  public function approvedTukarShiftRequests()
  {
    return $this->hasMany(TukarShiftRequest::class, 'approved_by', 'id');
  }

  public function approvedPerubahanLemburRequests()
  {
    return $this->hasMany(PerubahanLemburRequest::class, 'approved_by', 'id');
  }

  public function approvedDoubleShiftRequests()
  {
    return $this->hasMany(DoubleShiftRequest::class, 'approved_by', 'id');
  }

  public function requestLogs()
  {
    return $this->hasMany(RequestLog::class, 'actor_id', 'id');
  }

  public function lokasiId(): ?int
  {
    return $this->karyawan?->lokasi_id;
  }

  public function isAdministrator(): bool
  {
    return $this->hasRole('Superuser') || $this->hasRole('Administrator');
  }
}
