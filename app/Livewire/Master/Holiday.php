<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\Holiday as HolidayModel;

#[Title('Hari Libur')]
class Holiday extends Component
{
  use AuthorizesRequests, WithPagination;

  public $holidayId;
  public $tanggal;
  public $nama;
  public $is_national = true;
  public $isEdit    = false;
  public $deleteId  = null;

  public $search = '';

  protected $queryString = [
    'search' => [
      'except' => '',
      'as'     => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = HolidayModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%")
            ->orWhere('tanggal', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('tanggal', 'ASC');

    return view('livewire.master.work_config.holiday.index', [
      'data'       => $query->paginate(10),
      'title'      => 'Hari Libur',
      'hasActions' => auth()->user()->canAny([
        'holiday.edit',
        'holiday.delete',
        'holiday.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('holiday.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('holiday.edit');

    $this->resetValidation();
    $holiday = HolidayModel::find($id);

    if (!$holiday) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->holidayId    = $id;
    $this->tanggal      = $holiday->tanggal?->format('Y-m-d');
    $this->nama         = $holiday->nama;
    $this->is_national  = (bool) $holiday->is_national;
    $this->isEdit       = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'tanggal'     => ['required', 'date', Rule::unique('holiday', 'tanggal')->ignore($this->holidayId)],
        'nama'        => ['required', 'min:3'],
        'is_national' => ['boolean'],
      ],
      [
        'tanggal.required' => 'Tanggal wajib diisi.',
        'tanggal.date'     => 'Tanggal tidak valid.',
        'tanggal.unique'   => 'Tanggal libur sudah ada.',
        'nama.required'    => 'Nama hari libur wajib diisi.',
        'nama.min'         => 'Nama hari libur minimal 3 karakter.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('holiday.create');

    try {
      HolidayModel::create([
        'tanggal'     => $this->tanggal,
        'nama'        => trim($this->nama),
        'is_national' => (bool) $this->is_national,
      ]);

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal ditambah.'
      ]);
    }
  }

  public function updateData()
  {
    $this->authorize('holiday.edit');

    try {
      HolidayModel::find($this->holidayId)->update([
        'tanggal'     => $this->tanggal,
        'nama'        => trim($this->nama),
        'is_national' => (bool) $this->is_national,
      ]);

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal diupdate.'
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('holiday.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('holiday.delete');

    if ($this->deleteId) {
      try {
        HolidayModel::destroy($this->deleteId);
        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil dihapus.'
        ]);

        $this->deleteId = null;
        $this->dispatch('closeConfirmModal');
      } catch (\Exception $e) {
        $this->dispatch('closeConfirmModal');
        if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
          $this->dispatch('alert', [
            'type'    => 'error',
            'message' => 'Data tidak bisa dihapus karena masih digunakan.'
          ]);
        } else {
          $this->dispatch('alert', [
            'type'    => 'error',
            'message' => 'Terjadi kesalahan saat menghapus data.'
          ]);
        }
      }
    }
  }

  private function resetForm()
  {
    $this->reset([
      'holidayId',
      'tanggal',
      'nama',
    ]);

    $this->is_national = true;
  }
}
