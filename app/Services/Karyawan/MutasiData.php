<?php

namespace App\Services\Karyawan;

use App\Models\{
  Karyawan,
  Kontrak,
  Lokasi,
  Jabatan,
  Kategori,
  History
};

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MutasiData
{
  public function __construct(protected NikGenerator $nikGenerator) {}

  public function mutasi(array $data, array $new): bool
  {
    return DB::transaction(function () use ($data, $new) {

      $karyawan     = Karyawan::findOrFail($data['karyawanId']);
      $kategoriLama = (int) $karyawan->kategori_id;

      $kontrak = Kontrak::where('karyawan_id', $data['karyawanId'])
        ->where('status', 'aktif')
        ->latest()
        ->first();

      $lokasi           = Lokasi::findOrFail($data['lokasiId'] ?? $karyawan->lokasi_id);
      $kategori         = Kategori::findOrFail($data['kategoriId'] ?? $kategoriLama);
      $kategoriLamaName = Kategori::findOrFail($kategoriLama);
      $isProbation      = strtolower($kategori->nama) === 'probation';

      if ($isProbation) {
        $jabatan            = null;
        $data['jabatanId']  = null;
      } else {
        $targetJabatanId  = $data['jabatanId'] ?? $karyawan->jabatan_id;
        $jabatan          = $targetJabatanId ? Jabatan::with('unit.divisi')->find($targetJabatanId) : null;

        if (!$jabatan) {
          throw new \Exception('Jabatan wajib diisi untuk kategori non-probation.');
        }
      }

      $histJabatan = null;
      if ($data['jabatanId'] !== null) {
        $histJabatan = Jabatan::with('unit.divisi')->find($data['jabatanId']);
      }

      $nikFinal = $karyawan->nik;

      [$oldPrefix] = explode('-', $karyawan->nik);

      if ($isProbation) {
        $currentPrefix = strtolower(str_replace(' ', '', $lokasi->nama) . 'prob');

        $parts = explode('-', $karyawan->nik);
        $currentNikPrefix = $parts[0];

        if ($currentNikPrefix !== $currentPrefix) {
          $nikFinal = $this->nikGenerator->forProbationFromExisting($karyawan, $lokasi);
        }
      } else {
        $newPrefix = strtolower(
          $lokasi->kode .
            $jabatan->unit->divisi->kode .
            $jabatan->unit->kode
        );

        if ($oldPrefix !== $newPrefix) {
          $nikFinal = $this->nikGenerator->forMutasi($karyawan, $lokasi, $jabatan);
        }
      }

      $oldData = [
        'kategori_id'   => $kategoriLama,
        'lokasi_id'     => $karyawan->lokasi_id,
        'jabatan_id'    => $karyawan->jabatan_id,
        'nik'           => $karyawan->nik,
        'tgl_efektif'   => optional($karyawan->tgl_efektif)->format('Y-m-d'),
        'tgl_penetapan' => optional($karyawan->tgl_penetapan)->format('Y-m-d'),
      ];

      $newData = array_filter([
        'kategori_id'   => $data['kategoriId'],
        'lokasi_id'     => $data['lokasiId'],
        'jabatan_id'    => $data['jabatanId'],
        'nik'           => $nikFinal,
        'tgl_efektif'   => isset($new['efektif']) ? Carbon::parse($new['efektif'])->format('Y-m-d') : null,
        'tgl_penetapan' => isset($new['penetapan']) ? Carbon::parse($new['penetapan'])->format('Y-m-d') : null,
      ], fn($v) => $v !== null);

      $update = array_diff_assoc($newData, $oldData);

      if (empty($update)) {
        return false;
      }

      $karyawan->update($update);

      History::create([
        'jenis'         => 'mutasi data',
        'karyawan_id'   => $karyawan->id,
        'nama_karyawan' => $karyawan->nama,
        'kategori_nama' => $data['kategoriId'] ? $kategori->nama : null,
        'lokasi_nama'   => $data['lokasiId'] ? $lokasi->nama : null,
        'unit_nama'     => $histJabatan?->unit?->nama,
        'divisi_nama'   => $histJabatan?->unit?->divisi?->nama,
        'jabatan_nama'  => $histJabatan?->nama,
        'nik'           => $nikFinal,
        'telpon'        => null,
        'tgl_efektif'   => isset($new['efektif']) ? Carbon::parse($new['efektif'])->format('Y-m-d') : null,
        'tgl_mulai'     => isset($new['tmk']) ? Carbon::parse($new['tmk'])->format('Y-m-d') : null,
        'tgl_selesai'   => isset($new['thk']) ? Carbon::parse($new['thk'])->format('Y-m-d') : null,
        'tgl_penetapan' => isset($new['penetapan']) ? Carbon::parse($new['penetapan'])->format('Y-m-d') : null,
        'status'        => $karyawan->status ?? null,
        'keterangan'    => $keterangan ?? 'mutasi data',
      ]);

      if ($data['kategoriId'] && (int) $data['kategoriId'] !== $kategoriLama) {
        $oldKategoriName = $kategoriLamaName->nama;
        $newKategoriName = $kategori->nama;

        $this->handleKontrak($karyawan, $kontrak, (string) $oldKategoriName, (string) $newKategoriName, $new);
      }

      return true;
    });
  }

  protected function handleKontrak(Karyawan $karyawan, ?Kontrak $kontrak, string $oldKategori, string $newKategori, array $new): void
  {

    if ($oldKategori == 'pkwt' && $newKategori == 'pkwtt') {
      return;
    } else {
      $kontrakKe = ($kontrak?->kontrak_ke ?? 0) + 1;

      $data = [
        'karyawan_id'   => $karyawan->id,
        'nama_karyawan' => $karyawan->nama,
        'tgl_mulai'     => isset($new['tmk']) ? Carbon::parse($new['tmk'])->format('Y-m-d') : null,
        'tgl_selesai'   => isset($new['thk']) ? Carbon::parse($new['thk'])->format('Y-m-d') : null,
        'status'        => 'aktif',
        'kontrak_ke'    => $kontrakKe,
      ];

      Kontrak::create($data);
    }
  }
}
