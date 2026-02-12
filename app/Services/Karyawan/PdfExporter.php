<?php

namespace App\Services\Karyawan;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExporter
{
  public function __construct(protected KaryawanService $service) {}

  protected function imageToBase64(string $path): string
  {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);

    return 'data:image/' . $type . ';base64,' . base64_encode($data);
  }

  public function make(array $filters)
  {
    $baseQuery = $this->service->base();

    $result = $this->service->applyFilters($baseQuery, $filters);
    $query = $result['query'];
    $appliedFilters = $result['filters'];

    $data = $query
      ->with([
        'kategori',
        'jabatan.unit.divisi',
        'lokasi',
      ])
      ->orderBy('id')
      ->get();


    $logoPath = public_path('img/raharu-light.png');

    $logoBase64 = $this->imageToBase64($logoPath);

    return Pdf::loadView('exports.karyawan-pdf', compact('data', 'logoBase64', 'appliedFilters'))
      ->setPaper('A4', 'landscape');
  }
}
