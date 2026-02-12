<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Karyawan extends Model
{
  protected $table = 'karyawan';

  protected $fillable = [
    'kategori_id',
    'lokasi_id',
    'jabatan_id',
    'nik',
    'nama',
    'ktp',
    'agama',
    'alamat',
    'telpon',
    'jenis_kelamin',
    'marital',
    'pendidikan',
    'bpjs_tk',
    'bpjs_ks',
    'tgl_lahir',
    'tgl_masuk',
    'tgl_efektif',
    'tgl_keluar',
    'tgl_penetapan',
    'img',
    'status',
  ];

  protected $casts = [
    'tgl_lahir'     => 'date',
    'tgl_masuk'     => 'date',
    'tgl_efektif'   => 'date',
    'tgl_keluar'    => 'date',
    'tgl_penetapan' => 'date',
  ];

  public function kategori()
  {
    return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
  }

  public function lokasi()
  {
    return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
  }

  public function jabatan()
  {
    return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
  }

  public function kontraks()
  {
    return $this->hasMany(Kontrak::class);
  }

  public function histories()
  {
    return $this->hasMany(History::class, 'karyawan_id', 'id');
  }

  public function user()
  {
    return $this->hasOne(User::class, 'karyawan_id', 'id');
  }

  public function absensis()
  {
    return $this->hasMany(Absensi::class);
  }

  public function absensiHariIni()
  {
    return $this->hasOne(Absensi::class)
      ->whereDate('tanggal', now()->toDateString());
  }

  public function getUsiaAttribute()
  {
    if (!$this->tgl_lahir) {
      return null;
    }

    return $this->tgl_lahir->age;
  }

  public function scopeOnlyAktif($query)
  {
    return $query->where('status', 'aktif');
  }

  public function kontrakTerakhir()
  {
    return $this->hasOne(Kontrak::class)->latestOfMany();
  }

  public function kontrakAktif()
  {
    return $this->hasOne(Kontrak::class)
      ->where('status', 'aktif');
  }

  public function kontrakAktifUntukMutasi()
  {
    return $this->hasOne(Kontrak::class)
      ->where('status', 'aktif')
      ->whereDate('tgl_selesai', '>=', now()->toDateString());
  }

  public function scopeKontrakAkanHabis(Builder $query): Builder
  {
    return $query
      ->whereHas(
        'kategori',
        fn($q) =>
        $q->where('nama', 'pkwt')
      )
      ->whereHas(
        'kontrakAktif',
        fn($q) =>
        $q->whereBetween('tgl_selesai', [
          now()->subDays(30)->toDateString(),
          now()->addDays(14)->toDateString(),
        ])
      );
  }
}
