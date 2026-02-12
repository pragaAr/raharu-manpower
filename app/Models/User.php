<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasRoles;

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

  public function lokasiId(): ?int
  {
    return $this->karyawan?->lokasi_id;
  }

  public function isAdministrator(): bool
  {
    return $this->hasRole('Administrator') || $this->hasRole('Superuser');
  }
}
