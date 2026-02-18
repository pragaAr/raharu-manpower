<?php

namespace App\Services\Karyawan;

use App\Models\{
  Kategori,
  Lokasi,
  Divisi,
  Karyawan
};

use Illuminate\Database\Eloquent\Builder;

class BaseData
{
  public function base(): Builder
  {
    $user = auth()->user();
    $lokasiId = $user?->karyawan?->lokasi_id;

    return Karyawan::query()
      ->when(
        $lokasiId && !$user?->isAdministrator(),
        fn($q) => $q->where('lokasi_id', $lokasiId)
      );
  }

  public function applyFilters(Builder $query, array $filters): array
  {
    $applied = [];

    $query
      ->when(
        $filters['status'] ?? null,
        function ($q, $v) use (&$applied) {
          $q->where('status', $v);
          $applied['Status'] = ucfirst($v);
        },
        function ($q) use (&$applied) {
          $q->where('status', 'aktif');
          $applied['Status'] = 'Aktif';
        }
      )
      ->when(
        $filters['kategori_id'] ?? null,
        function ($q, $v) use (&$applied) {
          $q->where('kategori_id', $v);
          $applied['Kategori'] = Kategori::find($v)?->nama;
        }
      )
      ->when(
        ($filters['lokasi_id'] ?? null)
          && auth()->user()->hasRole(['Superuser', 'Administrator']),
        function ($q, $v) use (&$applied) {
          $q->where('lokasi_id', $v);
          $applied['Lokasi'] = Lokasi::find($v)?->nama;
        }
      )
      ->when(
        $filters['divisi_id'] ?? null,
        function ($q, $v) use (&$applied) {
          $q->whereHas(
            'jabatan.unit',
            fn($sub) =>
            $sub->where('divisi_id', $v)
          );
          $applied['Divisi'] = Divisi::find($v)?->nama;
        }
      )
      ->when(
        ($filters['tgl_masuk_start'] ?? null) && ($filters['tgl_masuk_end'] ?? null),
        function ($q) use ($filters, &$applied) {
          $q->whereBetween('tgl_masuk', [
            $filters['tgl_masuk_start'],
            $filters['tgl_masuk_end'],
          ]);

          $applied['Tanggal Masuk'] =
            $filters['tgl_masuk_start'] . ' s/d ' . $filters['tgl_masuk_end'];
        }
      )
      ->when(
        ($filters['tgl_masuk_start'] ?? null) && !($filters['tgl_masuk_end'] ?? null),
        function ($q) use ($filters, &$applied) {
          $q->where('tgl_masuk', '>=', $filters['tgl_masuk_start']);
          $applied['Tanggal Masuk ≥'] = $filters['tgl_masuk_start'];
        }
      )
      ->when(
        !($filters['tgl_masuk_start'] ?? null) && ($filters['tgl_masuk_end'] ?? null),
        function ($q) use ($filters, &$applied) {
          $q->where('tgl_masuk', '<=', $filters['tgl_masuk_end']);
          $applied['Tanggal Masuk ≤'] = $filters['tgl_masuk_end'];
        }
      );

    return [
      'query' => $query,
      'filters' => array_filter($applied),
    ];
  }

  public function applySearch(Builder $query, ?string $search): Builder
  {
    return $query->when($search, function ($q) use ($search) {
      $search = trim($search) . '%';

      $q->where(function ($sub) use ($search) {
        $sub->where('nik', 'like', $search)
          ->orWhere('nama', 'like', $search)
          ->orWhereHas('kategori', fn($q) => $q->where('nama', 'like', $search))
          ->orWhereHas('jabatan', fn($q) => $q->where('nama', 'like', $search))
          ->orWhereHas('lokasi', fn($q) => $q->where('nama', 'like', $search));
      });
    });
  }
}
