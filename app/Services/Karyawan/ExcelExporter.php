<?php

namespace App\Services\Karyawan;

use App\Exports\KaryawanExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExporter
{
  public function __construct(protected KaryawanService $service) {}

  public function download(array $filters)
  {
    $baseQuery = $this->service->base();

    $result = $this->service->applyFilters($baseQuery, $filters);
    $query  = $result['query'];
    $appliedFilters = $result['filters'];

    $data = $query
      ->with([
        'kategori',
        'jabatan.unit.divisi',
        'lokasi',
      ])
      ->orderBy('id')
      ->get();

    return Excel::download(
      new KaryawanExcelExport($data, $appliedFilters),
      'karyawan_' . now()->format('Y-m-d_His') . '.xlsx'
    );
  }
}
