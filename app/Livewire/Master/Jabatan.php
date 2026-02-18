<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};

use Livewire\Attributes\Title;

use App\Models\{
  Jabatan as JabatanModel,
  Unit
};

#[Title('Jabatan')]
class Jabatan extends Component
{
  use AuthorizesRequests, WithPagination;

  public $jabatanId;
  public $unit_id;
  public $nama;
  public $isEdit    = false;
  public $deleteId  = null;

  public $units = [];

  public $search = '';

  protected $queryString = [
    'search' => [
      'except'  => '',
      'as'      => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = JabatanModel::with('unit')
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%");
        })
          ->orWhereHas('unit', function ($u) {
            $u->where('nama', 'like', "%{$this->search}%");
          });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.jabatan', [
      'data'        => $query->paginate(10),
      'title'       => 'Jabatan',
      'hasActions'  => auth()->user()->canAny([
        'jabatan.edit',
        'jabatan.delete',
        'jabatan.create',
      ]),
    ]);
  }

  public function mount()
  {
    $this->units = Unit::join('divisi', 'unit.divisi_id', '=', 'divisi.id')
      ->orderBy('divisi.nama')
      ->get(['unit.id', 'unit.nama', 'divisi.nama as divisi_nama']);
  }

  public function create()
  {
    $this->authorize('jabatan.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal', unit_id: $this->unit_id);
  }

  public function edit($id)
  {
    $this->authorize('jabatan.edit');

    $this->resetValidation();
    $jabatan = JabatanModel::find($id);

    if (!$jabatan) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->nama       = $jabatan->nama;
    $this->jabatanId  = $id;
    $this->unit_id    = $jabatan->unit_id;
    $this->isEdit     = true;

    $this->dispatch('openModal', unit_id: $this->unit_id);
  }

  public function save()
  {
    $this->validate(
      [
        'unit_id' => ['required'],
        'nama' => ['required', 'min:3', Rule::unique('jabatan', 'nama')->where(fn($q) => $q->where('unit_id', $this->unit_id))->ignore($this->jabatanId)],
      ],
      [
        'unit_id.required'  => 'Unit wajib dipilih.',
        'nama.required'     => 'Jabatan wajib diisi.',
        'nama.min'          => 'Jabatan minimal 3 karakter.',
        'nama.unique'       => 'Jabatan sudah digunakan.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('jabatan.create');

    try {
      JabatanModel::create([
        'unit_id' => $this->unit_id,
        'nama'    => strtolower(trim($this->nama)),
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
    $this->authorize('jabatan.edit');

    try {
      JabatanModel::find($this->jabatanId)->update([
        'unit_id' => $this->unit_id,
        'nama'    => strtolower(trim($this->nama)),
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
    $this->authorize('jabatan.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('jabatan.delete');

    if ($this->deleteId) {
      try {
        JabatanModel::destroy($this->deleteId);
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
      'nama',
      'unit_id',
      'jabatanId',
    ]);
  }
}
