<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Services\Karyawan\BaseData;

class DashboardService
{
  public function __construct(protected BaseData $karyawan) {}

  public function getSummary(): array
  {
    $query = $this->karyawan->base();

    return [
      'total' => (clone $query)->count(),
      'aktif' => (clone $query)->where('status', 'aktif')->count(),
      'nonaktif' => (clone $query)->where('status', 'nonaktif')->count(),
      'kontrakHabis' => (clone $query)->kontrakAkanHabis()->count(),
    ];
  }

  public function getTrenKaryawan(): array
  {
    $start = Carbon::now()->startOfMonth()->subMonths(11);

    $data = $this->karyawan->base()
      ->select(
        DB::raw('COUNT(*) as total'),
        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan")
      )
      ->where('status', 'aktif')
      ->where('created_at', '>=', $start)
      ->groupBy('bulan')
      ->orderBy('bulan')
      ->get()
      ->keyBy('bulan');

    $labels = [];
    $values = [];

    for ($i = 0; $i < 12; $i++) {
      $month = $start->copy()->addMonths($i);
      $key = $month->format('Y-m');

      $labels[] = $month->translatedFormat('M Y');
      $values[] = $data[$key]->total ?? 0;
    }

    return compact('labels', 'values');
  }

  public function getDistribusiKategori(): array
  {
    $data = $this->karyawan->base()
      ->select('kategori_id', DB::raw('COUNT(*) as total'))
      ->where('status', 'aktif')
      ->groupBy('kategori_id')
      ->with('kategori:id,nama')
      ->get();

    return [
      'labels' => $data->pluck('kategori.nama')->toArray(),
      'data' => $data->pluck('total')->toArray(),
    ];
  }
}
