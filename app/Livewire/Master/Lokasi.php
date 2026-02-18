<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\Lokasi as LokasiModel;

#[Title('Lokasi')]
class Lokasi extends Component
{
  use AuthorizesRequests, WithPagination;

  public $nama;
  public $kode;
  public $lat;
  public $lng;
  public $lokasiId;
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
    $query = LokasiModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('nama', 'like', "%{$this->search}%")
            ->orWhere('kode', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.lokasi', [
      'data'        => $query->paginate(10),
      'title'       => 'Lokasi',
      'hasActions'  => auth()->user()->canAny([
        'lokasi.edit',
        'lokasi.delete',
        'lokasi.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('lokasi.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('lokasi.edit');

    $this->resetValidation();
    $lokasi = LokasiModel::find($id);

    if (!$lokasi) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->nama     = $lokasi->nama;
    $this->kode     = $lokasi->kode;
    $this->lat      = $lokasi->lat;
    $this->lng      = $lokasi->lng;
    $this->lokasiId = $id;
    $this->isEdit   = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'nama'  => ['required', 'min:3', Rule::unique('lokasi', 'nama')->ignore($this->lokasiId)],
        'kode'  => ['required', 'min:1', Rule::unique('lokasi', 'kode')->ignore($this->lokasiId)],
        'lat'   => ['required', Rule::unique('lokasi', 'lat')->ignore($this->lokasiId)],
        'lng'   => ['required', Rule::unique('lokasi', 'lng')->ignore($this->lokasiId)],
      ],
      [
        'nama.required' => 'Nama Lokasi wajib diisi.',
        'nama.min'      => 'Nama Lokasi minimal 3 karakter.',
        'nama.unique'   => 'Nama Lokasi sudah digunakan.',
        'kode.required' => 'Kode Lokasi wajib diisi.',
        'kode.min'      => 'Kode Lokasi minimal 1 karakter.',
        'kode.unique'   => 'Kode Lokasi sudah digunakan.',
        'lat.required'  => 'Latitude Lokasi wajib diisi.',
        'lat.unique'    => 'Latitude Lokasi sudah digunakan.',
        'lng.required'  => 'Longitude Lokasi wajib diisi.',
        'lng.unique'    => 'Longitude Lokasi sudah digunakan.',

      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('lokasi.create');

    try {
      LokasiModel::create([
        'nama'  => strtolower(trim($this->nama)),
        'kode'  => strtolower(trim($this->kode)),
        'lat'   => trim($this->lat),
        'lng'   => trim($this->lng),
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
    $this->authorize('lokasi.edit');

    try {
      LokasiModel::find($this->lokasiId)->update([
        'nama'  => strtolower(trim($this->nama)),
        'kode'  => strtolower(trim($this->kode)),
        'lat'   => trim($this->lat),
        'lng'   => trim($this->lng),
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
    $this->authorize('lokasi.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('lokasi.delete');

    if ($this->deleteId) {
      try {
        LokasiModel::destroy($this->deleteId);
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
      'lat',
      'lng',
      'lokasiId',
    ]);
  }
}
