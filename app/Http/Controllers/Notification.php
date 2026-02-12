<?php

namespace App\Http\Controllers;

use App\Services\Karyawan\BaseData;

class Notification extends Controller
{
  public function __construct(protected BaseData $karyawan) {}

  public function index()
  {
    $endedSoon = $this->karyawan
      ->base()
      ->with('kontrakAktif')
      ->kontrakAkanHabis()
      ->get();

    return response()->json([
      'kontrak_akan_habis' => $endedSoon->map(function ($karyawan) {
        $kontrak = $karyawan->kontrakAktif;

        if (!$kontrak) {
          return null;
        }

        $isExpired = $kontrak->tgl_selesai->isPast();

        return [
          'id' => $karyawan->id,
          'nik' => $karyawan->nik,
          'nama' => $karyawan->nama,
          'kontrak_ke' => $kontrak->kontrak_ke,
          'tgl_mulai' => $kontrak->tgl_mulai->format('Y-m-d'),
          'tgl_selesai' => $kontrak->tgl_selesai->format('Y-m-d'),

          'status_kontrak' => $isExpired ? 'sudah_habis' : 'akan_habis',
          'label' => $isExpired ? 'Kontrak sudah habis' : 'Kontrak akan habis',
          'text' => $isExpired ? 'Sudah habis' : 'Akan habis',
        ];
      })->filter()->values()
    ]);
  }
}
