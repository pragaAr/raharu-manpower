<?php

namespace App\Services\Karyawan;

use App\Models\{
  Karyawan,
  Kontrak,
  History,
  lokasi
};

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateStatus
{
  public function __construct(protected NikGenerator $nikGenerator) {}

  public function update(Karyawan $karyawan, string $statusBaru, array $data): bool
  {
    return DB::transaction(function () use ($karyawan, $statusBaru, $data) {

      $nikFinal   = $karyawan->nik;
      $statusLama = $karyawan->status;
      $lokasi     = Lokasi::findOrFail($data['lokasiId'] ?? $karyawan->lokasi_id);

      if ($statusLama === $statusBaru) {
        return false;
      }

      $kontrakTerakhir = Kontrak::where('karyawan_id', $karyawan->id)
        ->latest()
        ->first();

      if ($statusLama === 'aktif' && $statusBaru === 'nonaktif') {

        $karyawan->update([
          'status'     => 'nonaktif',
          'tgl_keluar' => $data['tgl_keluar'],
        ]);

        History::create([
          'jenis'         => 'nonaktif',
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'status'        => 'nonaktif',
          'tgl_keluar'    => $data['tgl_keluar'],
          'keterangan'    => $data['keterangan'],
        ]);

        if ($kontrakTerakhir) {
          $kontrakTerakhir->update([
            'status'      => 'nonaktif',
            'tgl_selesai' => $data['tgl_keluar'],
          ]);
        }

        return true;
      }

      if ($statusLama === 'aktif' && $statusBaru === 'vakum') {

        if ($karyawan->kategori?->nama !== 'pkwt') {
          throw new \Exception('Vakum hanya untuk karyawan PKWT.');
        }

        $karyawan->update([
          'status'      => 'vakum',
          'tgl_efektif' => $data['tgl_mulai'],
          'tgl_mulai'   => $data['tgl_mulai'],
          'tgl_selesai' => $data['tgl_selesai'],
        ]);

        History::create([
          'jenis'         => 'vakum',
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'status'        => 'vakum',
          'tgl_efektif'   => $data['tgl_mulai'],
          'tgl_mulai'     => $data['tgl_mulai'],
          'tgl_selesai'   => $data['tgl_selesai'],
          'keterangan'    => $data['keterangan'],
        ]);

        if ($kontrakTerakhir) {
          $kontrakTerakhir->update([
            'status'      => 'vakum',
            'tgl_selesai' => $data['tgl_selesai'],
          ]);
        }

        return true;
      }

      if ($statusLama === 'nonaktif' && $statusBaru === 'aktif') {

        $currentPrefix = strtolower(str_replace(' ', '', $lokasi->nama) . 'prob');

        $parts = explode('-', $karyawan->nik);
        $currentNikPrefix = $parts[0];

        if ($currentNikPrefix !== $currentPrefix) {
          $nikFinal = $this->nikGenerator->forProbationFromExisting($karyawan, $lokasi);
        }

        $karyawan->update([
          'nik'         => $nikFinal,
          'kategori_id' => 6,
          'jabatan_id'  => null,
          'status'      => 'aktif',
          'tgl_keluar'  => null,
          'tgl_masuk'   => $data['tgl_masuk'],
          'tgl_efektif' => $data['tgl_masuk'],
          'lokasi_id'   => $data['lokasi_id'],
        ]);

        History::create([
          'jenis'         => 'reaktifasi',
          'karyawan_id'   => $karyawan->id,
          'nik'           => $nikFinal,
          'kategori_nama' => 'probation',
          'nama_karyawan' => $karyawan->nama,
          'status'        => 'aktif',
          'tgl_efektif'   => $data['tgl_masuk'],
          'tgl_masuk'     => $data['tgl_masuk'],
          'keterangan'    => $data['keterangan'],
        ]);

        return true;
      }

      if ($statusLama === 'vakum' && $statusBaru === 'aktif') {

        $tglSelesai = Carbon::parse($data['tgl_mulai'])->addMonths(3)->toDateString();

        $karyawan->update([
          'status'      => 'aktif',
          'tgl_efektif' => $data['tgl_mulai'],
          'tgl_mulai'   => $data['tgl_mulai'],
          'tgl_selesai' => $tglSelesai,
        ]);

        History::create([
          'jenis'         => 'reaktifasi-kontrak',
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'tgl_efektif'   => $data['tgl_mulai'],
          'tgl_mulai'     => $data['tgl_mulai'],
          'tgl_selesai'   => $tglSelesai,
          'status'        => 'aktif',
          'keterangan'    => $data['keterangan'],
        ]);

        Kontrak::create([
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'status'        => 'aktif',
          'tgl_mulai'     => $data['tgl_mulai'],
          'tgl_selesai'   => $tglSelesai,
          'kontrak_ke'    => 1,
        ]);

        return true;
      }

      if ($statusLama === 'vakum' && $statusBaru === 'nonaktif') {

        $karyawan->update([
          'status'     => 'nonaktif',
          'tgl_keluar' => $data['tgl_keluar'],
        ]);

        History::create([
          'jenis'         => 'nonaktif',
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'status'        => 'nonaktif',
          'tgl_keluar'    => $data['tgl_keluar'],
          'keterangan'    => $data['keterangan'],
        ]);

        if ($kontrakTerakhir) {
          $kontrakTerakhir->update([
            'status'      => 'nonaktif',
            'tgl_selesai' => $data['tgl_keluar'],
          ]);
        }

        return true;
      }

      throw new \Exception('Perubahan status tidak valid.');
    });
  }
}
