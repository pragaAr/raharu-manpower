<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;

use Livewire\{
  Component,
  WithPagination
};

use Livewire\Attributes\{
  Title,
  On
};

use App\Models\{
  Kategori,
  Lokasi,
  Divisi,
  Jabatan
};

use App\Services\Karyawan\{
  BaseData,
  ExcelExporter,
  DeleteData
};

use App\Services\Log\AuditLogger;

#[Title('Karyawan')]
class Index extends Component
{
  use WithPagination, AuthorizesRequests;

  public $status;
  public $kategori;
  public $lokasi;
  public $divisi;
  public $jabatan;
  public $dari, $sampai;

  public $deleteId = null;

  protected $paginationTheme = 'bootstrap';

  public $search = '';
  public $lastPage = 1;

  public $kategoris = [], $lokasis = [], $divisis = [], $jabatans = [];

  protected array $allowedQuery = [
    'status',
    'kategori',
    'lokasi',
    'divisi',
    'jabatan',
    'dari',
    'sampai',
    's',
  ];

  public $draft = [
    'status'          => 'aktif',
    'kategori_id'     => null,
    'lokasi_id'       => null,
    'divisi_id'       => null,
    'jabatan_id'      => null,
    'tgl_masuk_start' => null,
    'tgl_masuk_end'   => null,
  ];

  protected $queryString = [
    'search'    => ['except' => '', 'as' => 's'],
    'status'    => ['except' => null],
    'kategori'  => ['except' => null],
    'lokasi'    => ['except' => null],
    'divisi'    => ['except' => null],
    'jabatan'   => ['except' => null],
    'dari'      => ['except' => null],
    'sampai'    => ['except' => null],
  ];

  private function nullIfBlank(mixed $value): mixed
  {
    return blank($value) ? null : $value;
  }

  protected function loadJabatanOptions(?int $divisiId = null)
  {
    return Jabatan::query()
      ->join('unit', 'jabatan.unit_id', '=', 'unit.id')
      ->when(
        $divisiId,
        fn($q) => $q->where('unit.divisi_id', $divisiId)
      )
      ->orderBy('jabatan.nama')
      ->get([
        'jabatan.id',
        'jabatan.nama',
        'unit.nama as unit_nama',
        'unit.divisi_id as divisi_id',
      ]);
  }

  public function hydrate()
  {
    foreach (['status', 'kategori', 'lokasi', 'divisi', 'jabatan', 'dari', 'sampai'] as $property) {
      if (blank($this->{$property} ?? null)) {
        $this->{$property} = null;
      }
    }
  }

  public function mount()
  {
    $incomingQuery = request()->query();
    $cleanQuery = $incomingQuery;

    foreach ($this->allowedQuery as $key) {
      if (array_key_exists($key, $cleanQuery) && blank($cleanQuery[$key])) {
        unset($cleanQuery[$key]);
      }
    }

    if ($cleanQuery !== $incomingQuery) {
      $url = request()->url();

      if ($cleanQuery) {
        $url .= '?' . Arr::query($cleanQuery);
      }

      $this->redirect($url, navigate: true);
      return;
    }

    $this->kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);
    $this->lokasis   = Lokasi::orderBy('nama')->get(['id', 'nama']);
    $this->divisis   = Divisi::orderBy('nama')->get(['id', 'nama']);

    $allowedStatus = ['aktif', 'nonaktif', 'vakum'];

    if (! in_array($this->status, $allowedStatus, true)) {
      $this->status = null;
    }

    if (! $this->kategori || ! Kategori::whereKey($this->kategori)->exists()) {
      $this->kategori = null;
    }

    if (
      ! $this->lokasi ||
      ! auth()->user()->hasRole(['Superuser', 'Administrator']) ||
      ! Lokasi::whereKey($this->lokasi)->exists()
    ) {
      $this->lokasi = null;
    }

    if (! $this->divisi || ! Divisi::whereKey($this->divisi)->exists()) {
      $this->divisi = null;
    }

    if (! $this->jabatan || ! Jabatan::whereKey($this->jabatan)->exists()) {
      $this->jabatan = null;
    }

    if ($this->divisi && $this->jabatan) {
      $validJabatan = Jabatan::whereKey($this->jabatan)
        ->whereHas('unit', fn($q) => $q->where('divisi_id', $this->divisi))
        ->exists();

      if (! $validJabatan) {
        $this->jabatan = null;
      }
    }

    $this->jabatans = $this->loadJabatanOptions($this->divisi);

    try {
      $this->dari   = $this->dari ? \Carbon\Carbon::parse($this->dari)->toDateString() : null;
      $this->sampai = $this->sampai ? \Carbon\Carbon::parse($this->sampai)->toDateString() : null;
    } catch (\Exception $e) {
      $this->dari = $this->sampai = null;
    }

    $this->draft = [
      'status'          => $this->status ?? 'aktif',
      'kategori_id'     => $this->kategori,
      'lokasi_id'       => $this->lokasi,
      'divisi_id'       => $this->divisi,
      'jabatan_id'      => $this->jabatan,
      'tgl_masuk_start' => $this->dari,
      'tgl_masuk_end'   => $this->sampai,
    ];
  }

  #[On('setKategori')]
  public function setKategori($id)
  {
    $this->draft['kategori_id'] = $id;
  }

  #[On('setLokasi')]
  public function setLokasi($id)
  {
    $this->draft['lokasi_id'] = $id;
  }

  #[On('setDivisi')]
  public function setDivisi($id)
  {
    $this->draft['divisi_id'] = $id;
  }

  public function updatedDraftDivisiId($divisiId)
  {
    $divisiId = $this->nullIfBlank($divisiId);
    $this->draft['divisi_id'] = $divisiId;

    $this->jabatans = $this->loadJabatanOptions($divisiId);

    $selected = $this->draft['jabatan_id'] ?? null;
    if ($selected) {
      $exists = $this->jabatans
        ->firstWhere('id', (int) $selected);

      if (! $exists) {
        $selected = null;
        $this->draft['jabatan_id'] = null;
      }
    }

    $this->dispatch('refresh-jabatan-filter', [
      'jabatans' => $this->jabatans,
      'selected' => $selected,
    ]);
  }

  #[On('setJabatan')]
  public function setJabatan($id)
  {
    $this->draft['jabatan_id'] = $id;
  }

  public function filter()
  {
    $draftStatus = $this->draft['status'] ?? 'aktif';

    $this->status = ($draftStatus === 'aktif' || blank($draftStatus))
      ? null
      : $draftStatus;

    $this->kategori = $this->nullIfBlank($this->draft['kategori_id'] ?? null);
    $this->lokasi   = $this->nullIfBlank($this->draft['lokasi_id'] ?? null);
    $this->divisi   = $this->nullIfBlank($this->draft['divisi_id'] ?? null);
    $this->jabatan  = $this->nullIfBlank($this->draft['jabatan_id'] ?? null);
    $this->dari     = $this->nullIfBlank($this->draft['tgl_masuk_start'] ?? null);
    $this->sampai   = $this->nullIfBlank($this->draft['tgl_masuk_end'] ?? null);

    if ($this->divisi && $this->jabatan) {
      $validJabatan = Jabatan::whereKey($this->jabatan)
        ->whereHas('unit', fn($q) => $q->where('divisi_id', $this->divisi))
        ->exists();

      if (! $validJabatan) {
        $this->jabatan = null;
        $this->draft['jabatan_id'] = null;
      }
    }

    $this->jabatans = $this->loadJabatanOptions($this->divisi);

    $this->dispatch('closeFilter');
    $this->resetPage();
  }

  public function hasActiveFilters(): bool
  {
    $filters = $this->filters;

    return ($filters['kategori_id'] ?? null)
      || ($filters['lokasi_id'] ?? null)
      || ($filters['divisi_id'] ?? null)
      || ($filters['jabatan_id'] ?? null)
      || (($filters['status'] ?? 'aktif') !== 'aktif')
      || ($filters['tgl_masuk_start'] ?? null)
      || ($filters['tgl_masuk_end'] ?? null);
  }

  public function resetFilter()
  {
    $this->status   = null;
    $this->kategori = null;
    $this->lokasi   = null;
    $this->divisi   = null;
    $this->jabatan  = null;
    $this->dari     = null;
    $this->sampai   = null;

    $this->draft = [
      'status'          => 'aktif',
      'kategori_id'     => null,
      'lokasi_id'       => null,
      'divisi_id'       => null,
      'jabatan_id'      => null,
      'tgl_masuk_start' => null,
      'tgl_masuk_end'   => null,
    ];

    $this->jabatans = $this->loadJabatanOptions();
    $this->dispatch('refresh-jabatan-filter', [
      'jabatans' => $this->jabatans,
      'selected' => null,
    ]);

    $this->dispatch('reset-select');
    $this->dispatch('closeFilter');

    $this->resetPage();
  }

  public function updatingSearch($value)
  {
    if (filled($value) && $this->search === '') {
      $this->lastPage = $this->getPage();
      $this->resetPage();
    }
  }

  public function updatedSearch($value)
  {
    if (blank($value)) {
      $this->setPage($this->lastPage);
    }
  }

  public function getFiltersProperty(): array
  {
    return array_filter([
      'status'          => $this->status ?? 'aktif',
      'kategori_id'     => $this->kategori,
      'lokasi_id'       => $this->lokasi,
      'divisi_id'       => $this->divisi,
      'jabatan_id'      => $this->jabatan,
      'tgl_masuk_start' => $this->dari,
      'tgl_masuk_end'   => $this->sampai,
    ]);
  }

  public function render(BaseData $service)
  {
    $base     = $service->base();
    $result   = $service->applyFilters($base, $this->filters);
    $query    = $result['query'];
    $filterOn = $result['filters'];

    if (($filterOn['Status'] ?? null) === 'Aktif' && count($filterOn) === 1) {
      $filterOn = [];
    }

    $query = $service->applySearch($query, $this->search);

    $data = $query
      ->select([
        'id',
        'nik',
        'nama',
        'jenis_kelamin',
        'tgl_lahir',
        'status',
        'kategori_id',
        'jabatan_id',
        'lokasi_id',
      ])
      ->with([
        'kategori:id,nama',
        'jabatan:id,nama',
        'lokasi:id,nama',
      ])
      ->orderBy('id')
      ->paginate(10);

    return view('livewire.karyawan.index', [
      'data'            => $data,
      'title'           => 'Karyawan',
      'displayFilters'  => $filterOn,
      'hasActions'      => auth()->user()->canAny([
        'karyawan.edit',
        'karyawan.delete',
        'karyawan.create',
        'karyawan.detail',
        'karyawan.mutasi',
        'karyawan.renewal',
      ]),
    ]);
  }

  public function edit($id)
  {
    $this->dispatch('open-edit', id: $id);
  }

  public function openFilter()
  {
    $this->dispatch('openFilter');
  }

  public function openExport()
  {
    $this->dispatch('openExport');
  }

  public function updateStatus($id)
  {
    $this->dispatch('open-update-status', id: $id);
  }

  public function confirmDelete($id)
  {
    $this->authorize('karyawan.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData(DeleteData $service)
  {
    $this->authorize('karyawan.delete');

    if ($this->deleteId) {
      try {
        $service->delete($this->deleteId);
        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil dihapus.'
        ]);
        $this->deleteId = null;
        $this->dispatch('closeConfirmModal');
      } catch (\Exception $e) {
        AuditLogger::error('Delete karyawan gagal', [
          'karyawan_id' => $this->deleteId,
          'error'       => $e->getMessage(),
        ]);

        $this->dispatch('closeConfirmModal');
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Terjadi kesalahan saat menghapus data.'
        ]);
      }
    }
  }

  public function exportExcel(ExcelExporter $exporter)
  {
    return $exporter->download($this->draft);
  }

  public function exportPdf()
  {
    session(['karyawan_export_pdf' => $this->draft]);

    $this->dispatch('open-pdf');
  }

  #[On('karyawan-updated')]
  public function refreshList() {}
}
