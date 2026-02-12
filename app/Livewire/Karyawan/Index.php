<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

#[Title('Karyawan')]
class Index extends Component
{
  use WithPagination, AuthorizesRequests;

  public $status;
  public $kategori;
  public $lokasi;
  public $divisi;
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
    'dari',
    'sampai',
    's',
  ];

  public $draft = [
    'status'          => 'aktif',
    'kategori_id'     => null,
    'lokasi_id'       => null,
    'divisi_id'       => null,
    'tgl_masuk_start' => null,
    'tgl_masuk_end'   => null,
  ];

  public $filters = [];

  protected $queryString = [
    'search'    => ['except' => '', 'as' => 's'],
    'status'    => ['except' => null],
    'kategori'  => ['except' => null],
    'lokasi'    => ['except' => null],
    'divisi'    => ['except' => null],
    'dari'      => ['except' => null],
    'sampai'    => ['except' => null],
  ];

  public function mount()
  {
    $this->kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);
    $this->lokasis   = Lokasi::orderBy('nama')->get(['id', 'nama']);
    $this->divisis   = Divisi::orderBy('nama')->get(['id', 'nama']);
    $this->jabatans  = Jabatan::orderBy('nama')->get(['id', 'nama']);

    $allowedStatus = ['aktif', 'nonaktif', 'vakum'];

    if (in_array($this->status, $allowedStatus, true)) {
      if ($this->status !== 'aktif') {
        $this->draft['status'] = $this->status;
      }
    } else {
      $this->status = null;
    }

    if ($this->kategori && Kategori::whereKey($this->kategori)->exists()) {
      $this->draft['kategori_id'] = $this->kategori;
    } else {
      $this->kategori = null;
    }

    if (
      $this->lokasi &&
      auth()->user()->hasRole('Administrator') &&
      Lokasi::whereKey($this->lokasi)->exists()
    ) {
      $this->draft['lokasi_id'] = $this->lokasi;
    } else {
      $this->lokasi = null;
    }

    if ($this->divisi && Divisi::whereKey($this->divisi)->exists()) {
      $this->draft['divisi_id'] = $this->divisi;
    } else {
      $this->divisi = null;
    }

    try {
      if ($this->dari) {
        $this->draft['tgl_masuk_start'] =
          \Carbon\Carbon::parse($this->dari)->toDateString();
      }

      if ($this->sampai) {
        $this->draft['tgl_masuk_end'] =
          \Carbon\Carbon::parse($this->sampai)->toDateString();
      }
    } catch (\Exception $e) {
      $this->dari = $this->sampai = null;
    }

    $this->filters = array_filter($this->draft);
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

  public function filter()
  {
    $this->filters  = array_filter($this->draft);
    $this->status   = ($this->filters['status'] ?? 'aktif') === 'aktif' ? null : $this->filters['status'];

    $this->kategori = $this->filters['kategori_id'] ?? null;
    $this->lokasi   = $this->filters['lokasi_id'] ?? null;
    $this->divisi   = $this->filters['divisi_id'] ?? null;
    $this->dari     = $this->filters['tgl_masuk_start'] ?? null;
    $this->sampai   = $this->filters['tgl_masuk_end'] ?? null;

    $this->dispatch('closeFilter');
    $this->resetPage();
  }

  public function hasActiveFilters(): bool
  {
    if (empty($this->filters)) {
      return false;
    }

    return ($this->filters['kategori_id'] ?? null)
      || ($this->filters['lokasi_id'] ?? null)
      || ($this->filters['divisi_id'] ?? null)
      || (($this->filters['status'] ?? 'aktif') !== 'aktif')
      || ($this->filters['tgl_masuk_start'] ?? null)
      || ($this->filters['tgl_masuk_end'] ?? null);
  }

  public function resetFilter()
  {
    $this->draft = [
      'status'          => 'aktif',
      'kategori_id'     => null,
      'lokasi_id'       => null,
      'divisi_id'       => null,
      'tgl_masuk_start' => null,
      'tgl_masuk_end'   => null,
    ];

    $this->filters = [];

    $this->kategori = null;
    $this->lokasi   = null;
    $this->divisi   = null;
    $this->dari     = null;
    $this->sampai   = null;
    $this->status   = null;

    $this->dispatch('reset-tomselect');
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

    if (!$this->deleteId) return;

    $service->delete($this->deleteId);

    $this->dispatch('alert', [
      'type'    => 'success',
      'message' => 'Data berhasil dihapus.',
    ]);

    $this->deleteId = null;
    $this->dispatch('closeConfirmModal');
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
