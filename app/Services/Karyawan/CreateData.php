<?php

namespace App\Services\Karyawan;

use App\Models\{
  Karyawan,
  Lokasi,
  Jabatan,
  Kategori,
  Kontrak,
  History,
  User
};

use Illuminate\Support\Facades\{
  DB,
  Hash
};
use Carbon\Carbon;

class CreateData
{
  public function __construct(protected NikGenerator $nikGenerator) {}

  public function create(array $data): Karyawan
  {
    return DB::transaction(function () use ($data) {

      $lokasi   = Lokasi::findOrFail($data['lokasi_id']);
      $kategori = Kategori::findOrFail($data['kategori_id']);
      $jabatan  = Jabatan::with('unit.divisi')
        ->find($data['jabatan_id']);

      $isProbation = $kategori && strtolower($kategori->nama) === 'probation';

      $nik = $isProbation ? $this->nikGenerator->forProbation($lokasi) : $this->nikGenerator->generate($lokasi, $jabatan);
      $nikNumber = (int) substr($nik, strrpos($nik, '-') + 1);

      $karyawan = Karyawan::create([
        'kategori_id'   => $kategori->id,
        'lokasi_id'     => $lokasi->id,
        'jabatan_id'    => $jabatan?->id,
        'nik'           => $nik,
        'nama'          => strtolower(trim($data['nama'])),
        'ktp'           => $data['ktp'],
        'agama'         => strtolower(trim($data['agama'])),
        'alamat'        => strtolower(trim($data['alamat'])),
        'telpon'        => $data['telpon'],
        'jenis_kelamin' => strtolower($data['jenis_kelamin']),
        'marital'       => strtolower($data['marital']),
        'pendidikan'    => strtolower($data['pendidikan']),
        'bpjs_tk'       => $data['bpjs_tk'] ?? null,
        'bpjs_ks'       => $data['bpjs_ks'] ?? null,
        'tgl_lahir'     => $data['tgl_lahir'] ? Carbon::parse($data['tgl_lahir'])->format('Y-m-d') : null,
        'tgl_masuk'     => $data['tgl_masuk'] ? Carbon::parse($data['tgl_masuk'])->format('Y-m-d') : null,
        'tgl_efektif'   => $data['tgl_efektif'] ? Carbon::parse($data['tgl_efektif'])->format('Y-m-d') : null,
        'tgl_penetapan' => isset($data['tgl_penetapan']) ? Carbon::parse($data['tgl_penetapan'])->format('Y-m-d') : null,
        'img'           => $data['img'],
        'status'        => 'aktif',
      ]);

      History::create([
        'jenis'         => 'penambahan data',
        'karyawan_id'   => $karyawan->id,
        'nama_karyawan' => $karyawan->nama,
        'kategori_nama' => $kategori->nama,
        'lokasi_nama'   => $lokasi->nama,
        'unit_nama'     => $jabatan?->unit?->nama,
        'divisi_nama'   => $jabatan?->unit?->divisi?->nama,
        'jabatan_nama'  => $jabatan?->nama,
        'nik'           => $karyawan->nik,
        'telpon'        => $karyawan->telpon,
        'tgl_masuk'     => $karyawan->tgl_masuk ? Carbon::parse($karyawan->tgl_masuk)->format('Y-m-d') : null,
        'tgl_efektif'   => $karyawan->tgl_efektif ? Carbon::parse($karyawan->tgl_efektif)->format('Y-m-d') : null,
        'tgl_mulai'     => $data['tgl_mulai'] ? Carbon::parse($data['tgl_mulai'])->format('Y-m-d') : null,
        'tgl_selesai'   => $data['tgl_selesai'] ? Carbon::parse($data['tgl_selesai'])->format('Y-m-d') : null,
        'tgl_penetapan' => $karyawan->tgl_penetapan ? Carbon::parse($karyawan->tgl_penetapan)->format('Y-m-d') : null,
        'status'        => $karyawan->status,
        'keterangan'    => $data['keterangan'] ?? 'karyawan baru',
      ]);

      $kategoriCheck = strtolower($kategori->nama);
      if (!in_array($kategoriCheck, ['harian', 'magang'])) {
        $password = $karyawan->tgl_lahir
          ? $karyawan->tgl_lahir->format('dmY')
          : 'h12345678_';

        $user = User::create([
          'username'    => 'kry' . $nikNumber,
          'karyawan_id' => $karyawan->id,
          'password'    => Hash::make($password),
        ]);

        $user->assignRole('Karyawan');
      }

      $isPkwt = $kategori && strtolower($kategori->nama) === 'pkwt';

      if ($isPkwt) {
        if (empty($data['tgl_mulai']) || empty($data['tgl_selesai'])) {
          throw new \Exception('TMK & THK wajib diisi untuk PKWT');
        }

        Kontrak::create([
          'karyawan_id'   => $karyawan->id,
          'nama_karyawan' => $karyawan->nama,
          'kontrak_ke'    => 1,
          'tgl_mulai'     => $data['tgl_mulai'] ? Carbon::parse($data['tgl_mulai'])->format('Y-m-d') : null,
          'tgl_selesai'   => $data['tgl_selesai'] ? Carbon::parse($data['tgl_selesai'])->format('Y-m-d') : null,
          'status'        => $karyawan->status,
        ]);
      }

      return $karyawan;
    });
  }
}
