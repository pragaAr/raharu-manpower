<?php

namespace App\Services\Karyawan;

use App\Models\Karyawan;

class UpdateData
{
  public function __construct(protected PhotoUpload $photoUploadService) {}

  public function update(Karyawan $karyawan, array $data, $fotoUpload = null): void
  {
    if ($fotoUpload) {
      $imgPath = $this->photoUploadService->handleUpload($fotoUpload);

      if (
        $karyawan->img &&
        $karyawan->img !== 'uploads/default.webp' &&
        file_exists(public_path($karyawan->img))
      ) {
        unlink(public_path($karyawan->img));
      }
    } else {
      $imgPath = $karyawan->img;
    }

    $karyawan->update([
      'nama'          => strtolower(trim($data['nama'])),
      'ktp'           => $data['ktp'],
      'agama'         => strtolower(trim($data['agama'])),
      'alamat'        => strtolower(trim($data['alamat'])),
      'telpon'        => strtolower(trim($data['telpon'])),
      'jenis_kelamin' => strtolower(trim($data['jk'])),
      'marital'       => strtolower(trim($data['marital'])),
      'pendidikan'    => strtolower(trim($data['pendidikan'])),
      'bpjs_tk'       => strtolower(trim($data['bpjsTk'] ?? '')),
      'bpjs_ks'       => strtolower(trim($data['bpjsKs'] ?? '')),
      'tgl_lahir'     => $data['tglLahir'] ?? null,
      'img'           => $imgPath,
    ]);
  }
}
