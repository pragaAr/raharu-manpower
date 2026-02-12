<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\Divisi as DivisiModel;

#[Title('Divisi')]
class Divisi extends Component
{
  use AuthorizesRequests, WithPagination;

  public $divisiId;
  public $nama;
  public $kode;
  public $isEdit    = false;
  public $deleteId  = null;

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
    $query = DivisiModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%")
            ->orWhere('kode', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.divisi', [
      'data'      => $query->paginate(10),
      'title'     => 'Divisi',
      'hasActions' => auth()->user()->canAny([
        'divisi.edit',
        'divisi.delete',
        'divisi.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('divisi.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('divisi.edit');

    $this->resetValidation();
    $divisi = DivisiModel::find($id);

    if (!$divisi) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->nama     = $divisi->nama;
    $this->kode     = $divisi->kode;
    $this->divisiId = $id;
    $this->isEdit   = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'nama' => ['required', 'min:3', \Illuminate\Validation\Rule::unique('divisi', 'nama')->ignore($this->divisiId)],
        'kode' => ['required', 'min:1', \Illuminate\Validation\Rule::unique('divisi', 'kode')->ignore($this->divisiId)],
      ],
      [
        'nama.required' => 'Divisi wajib diisi.',
        'nama.min'      => 'Divisi minimal 3 karakter.',
        'nama.unique'   => 'Divisi sudah digunakan.',
        'kode.required' => 'Kode wajib diisi.',
        'kode.min'      => 'Kode minimal 1 karakter.',
        'kode.unique'   => 'Kode sudah digunakan.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('divisi.create');

    try {
      DivisiModel::create([
        'nama' => strtolower(trim($this->nama)),
        'kode' => strtolower(trim($this->kode)),
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
    $this->authorize('divisi.edit');

    try {
      DivisiModel::find($this->divisiId)->update([
        'nama' => strtolower(trim($this->nama)),
        'kode' => strtolower(trim($this->kode)),
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
    $this->authorize('divisi.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('divisi.delete');

    if ($this->deleteId) {
      try {
        DivisiModel::destroy($this->deleteId);

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
      'kode',
      'divisiId',
    ]);
  }
}
