<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{
  Unit as UnitModel,
  Divisi
};

#[Title('Unit')]
class Unit extends Component
{
  use AuthorizesRequests, WithPagination;

  public $nama;
  public $kode;
  public $divisi_id;
  public $unitId;
  public $isEdit    = false;
  public $deleteId  = null;

  public $divisis = [];

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
    $this->divisis = Divisi::orderBy('nama')->get();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $query = UnitModel::with('divisi')
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%")
            ->orWhere('kode', 'like', "%{$this->search}%")
            ->orWhereHas('divisi', function ($d) {
              $d->where('nama', 'like', "%{$this->search}%");
            });
        });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.unit', [
      'data'        => $query->paginate(10),
      'title'       => 'Unit',
      'hasActions'  => auth()->user()->canAny([
        'unit.edit',
        'unit.delete',
        'unit.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('unit.create');

    $this->resetValidation();
    $this->resetForm();

    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('unit.edit');

    $this->resetValidation();
    $unit = UnitModel::find($id);

    if (!$unit) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->nama       = $unit->nama;
    $this->kode       = $unit->kode;
    $this->divisi_id  = $unit->divisi_id;
    $this->unitId     = $id;
    $this->isEdit     = true;

    $this->dispatch('openModal', divisi_id: $this->divisi_id);
  }

  public function save()
  {
    $this->validate(
      [
        'divisi_id' => 'required',
        'nama' => ['required', \Illuminate\Validation\Rule::unique('unit', 'nama')->ignore($this->unitId)],
        'kode' => ['required', \Illuminate\Validation\Rule::unique('unit', 'kode')->ignore($this->unitId)],
      ],
      [
        'divisi_id.required'  => 'Divisi wajib dipilih.',
        'nama.required'       => 'Nama Unit wajib diisi.',
        'nama.unique'         => 'Nama Unit sudah digunakan.',
        'kode.required'       => 'Kode Unit wajib diisi.',
        'kode.unique'         => 'Kode Unit sudah digunakan.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('unit.create');

    try {
      UnitModel::create([
        'divisi_id' => $this->divisi_id,
        'nama'      => strtolower(trim($this->nama)),
        'kode'      => strtolower(trim($this->kode)),
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
    $this->authorize('unit.edit');

    try {
      UnitModel::find($this->unitId)->update([
        'divisi_id' => $this->divisi_id,
        'nama'      => strtolower(trim($this->nama)),
        'kode'      => strtolower(trim($this->kode)),
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
    $this->authorize('unit.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('unit.delete');

    if ($this->deleteId) {
      try {
        UnitModel::destroy($this->deleteId);
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

  public function resetForm()
  {
    $this->reset([
      'nama',
      'kode',
      'divisi_id',
      'unitId',
    ]);
  }
}
