<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\Kategori as KategoriModel;

#[Title('Kategori')]
class Kategori extends Component
{
  use AuthorizesRequests, WithPagination;

  public $nama;
  public $keterangan;
  public $kategoriId;
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
    $query = KategoriModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%")
            ->orWhere('keterangan', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.kategori', [
      'data'        => $query->paginate(10),
      'title'       => 'Kategori',
      'hasActions'  => auth()->user()->canAny([
        'kategori.edit',
        'kategori.delete',
        'kategori.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('kategori.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('kategori.edit');

    $this->resetValidation();
    $kategori = KategoriModel::find($id);

    if (!$kategori) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->nama       = $kategori->nama;
    $this->keterangan = $kategori->keterangan;
    $this->kategoriId = $id;
    $this->isEdit     = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'nama'        => ['required', 'min:3', Rule::unique('kategori', 'nama')->ignore($this->kategoriId)],
        'keterangan'  => ['required', 'min:6', Rule::unique('kategori', 'keterangan')->ignore($this->kategoriId)],
      ],
      [
        'nama.required'       => 'Nama Kategori wajib diisi.',
        'nama.min'            => 'Nama Kategori minimal 3 karakter.',
        'nama.unique'         => 'Nama Kategori sudah digunakan.',
        'keterangan.required' => 'Keterangan Kategori wajib diisi.',
        'keterangan.min'      => 'Keterangan Kategori minimal 6 karakter.',
        'keterangan.unique'   => 'Keterangan Kategori sudah digunakan.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('kategori.create');

    try {
      KategoriModel::create([
        'nama'        => strtolower(trim($this->nama)),
        'keterangan'  => strtolower(trim($this->keterangan)),
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
    $this->authorize('kategori.edit');

    try {
      KategoriModel::find($this->kategoriId)->update([
        'nama'        => strtolower(trim($this->nama)),
        'keterangan'  => strtolower(trim($this->keterangan)),
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
    $this->authorize('kategori.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('kategori.delete');

    if ($this->deleteId) {
      try {
        KategoriModel::destroy($this->deleteId);
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
      'keterangan',
      'kategoriId',
    ]);
  }
}
