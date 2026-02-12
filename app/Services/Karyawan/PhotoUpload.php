<?php

namespace App\Services\Karyawan;

class PhotoUpload
{
  public function handleUpload($fotoUpload): string
  {
    if ($fotoUpload) {
      $filename = time() . '_' . uniqid() . '.' . $fotoUpload->getClientOriginalExtension();
      $imgPath = 'uploads/' . $filename;

      copy(
        $fotoUpload->getRealPath(),
        public_path($imgPath)
      );

      return $imgPath;
    }

    return 'uploads/default.webp';
  }
}
