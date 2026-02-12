<?php

namespace App\Livewire\Absensi;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{Absensi, AbsensiLog, Karyawan};

#[Title('Absensi')]
class Index extends Component
{
  use AuthorizesRequests, WithPagination;

  public $absenId;
  public $karyawanId;
  public $tanggal;
  public $masuk;
  public $pulang;
  public $source    = 'manpower';
  public $inputBy;
  public $keterangan;
  public $isEdit    = false;
  public $deleteId  = null;

  public $karyawans = [];

  public $search = '';

  protected $queryString = [
    'search' => [
      'except'  => '',
      'as'      => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->karyawans = Karyawan::orderBy('id')->get();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = Absensi::with([
      'karyawan',
      'lastLog.inputBy',
    ])
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('tanggal', 'like', "%{$this->search}%")
            ->orWhereHas('karyawan', function ($d) {
              $d->where('nik', 'like', "%{$this->search}%")
                ->orWhere('nama', 'like', "%{$this->search}%");
            })
            ->orWhereHas('lastLog', function ($l) {
              $l->where('source', 'like', "%{$this->search}%")
                ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        });
      })
      ->orderBy('tanggal', 'desc');

    return view('livewire.absensi.index', [
      'data'        => $query->paginate(10),
      'title'       => 'Absensi',
      'hasActions'  => auth()->user()->canAny([
        'absensi.edit',
        'absensi.delete',
        'absensi.create',
        'absensi.detail',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('absensi.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function confirmDelete($id)
  {
    $this->authorize('absensi.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('absensi.delete');

    if ($this->deleteId) {
      try {
        Absensi::destroy($this->deleteId);
        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil dihapus.'
        ]);
        $this->deleteId = null;
        $this->dispatch('closeConfirmModal');
      } catch (\Exception $e) {
        $this->dispatch('closeConfirmModal');
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Terjadi kesalahan saat menghapus data.'
        ]);
      }
    }
  }

  public function resetForm()
  {
    $this->reset([
      'karyawanId',
      'tanggal',
      'masuk',
      'pulang',
      'source',
      'inputBy',
      'keterangan',
    ]);
  }
}
