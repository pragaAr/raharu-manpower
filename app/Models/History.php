<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
  protected $table = 'history';

  protected $fillable = [
    'jenis',
    'karyawan_id',
    'nama_karyawan',
    'kategori_nama',
    'unit_nama',
    'divisi_nama',
    'jabatan_nama',
    'lokasi_nama',
    'nik',
    'telpon',
    'tgl_masuk',
    'tgl_efektif',
    'tgl_keluar',
    'tgl_mulai',
    'tgl_selesai',
    'tgl_penetapan',
    'status',
    'keterangan',
  ];

  protected $casts = [
    'tgl_masuk' => 'date',
    'tgl_efektif' => 'date',
    'tgl_keluar' => 'date',
    'tgl_mulai' => 'date',
    'tgl_selesai' => 'date',
    'tgl_penetapan' => 'date',
  ];

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
  }
}
