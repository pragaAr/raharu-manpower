<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\ShiftMaster as ShiftModel;

#[Title('Shift')]
class Shift extends Component
{
  use AuthorizesRequests, WithPagination;

  public $shiftId;
  public $nama;
  public $jam_masuk;
  public $jam_pulang;
  public $is_active = true;
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
    $query = ShiftModel::query()
      ->when($this->search, function ($q) {
        $q->where('nama', 'like', "%{$this->search}%");
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.shift', [
      'data'       => $query->paginate(10),
      'title'      => 'Shift',
      'hasActions' => auth()->user()->canAny([
        'shift.edit',
        'shift.delete',
        'shift.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('shift.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('shift.edit');

    $this->resetValidation();
    $shift = ShiftModel::find($id);

    if (!$shift) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->shiftId    = $id;
    $this->nama       = $shift->nama;
    $this->jam_masuk  = $shift->jam_masuk?->format('H:i');
    $this->jam_pulang = $shift->jam_pulang?->format('H:i');
    $this->is_active  = (bool) $shift->is_active;
    $this->isEdit     = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'nama'       => ['required', 'min:2', Rule::unique('shift_master', 'nama')->ignore($this->shiftId)],
        'jam_masuk'  => ['required', 'date_format:H:i'],
        'jam_pulang' => ['required', 'date_format:H:i'],
        'is_active'  => ['boolean'],
      ],
      [
        'nama.required'       => 'Nama shift wajib diisi.',
        'nama.min'            => 'Nama shift minimal 2 karakter.',
        'nama.unique'         => 'Nama shift sudah digunakan.',
        'jam_masuk.required'  => 'Jam masuk wajib diisi.',
        'jam_masuk.date_format' => 'Format jam masuk tidak valid (HH:MM).',
        'jam_pulang.required' => 'Jam pulang wajib diisi.',
        'jam_pulang.date_format' => 'Format jam pulang tidak valid (HH:MM).',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('shift.create');

    try {
      ShiftModel::create([
        'nama'       => trim($this->nama),
        'jam_masuk'  => $this->jam_masuk,
        'jam_pulang' => $this->jam_pulang,
        'is_active'  => (bool) $this->is_active,
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
    $this->authorize('shift.edit');

    try {
      ShiftModel::find($this->shiftId)->update([
        'nama'       => trim($this->nama),
        'jam_masuk'  => $this->jam_masuk,
        'jam_pulang' => $this->jam_pulang,
        'is_active'  => (bool) $this->is_active,
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
    $this->authorize('shift.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('shift.delete');

    if ($this->deleteId) {
      try {
        ShiftModel::destroy($this->deleteId);
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
      'shiftId',
      'nama',
      'jam_masuk',
      'jam_pulang',
    ]);

    $this->is_active = true;
  }
}
