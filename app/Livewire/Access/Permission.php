<?php

namespace App\Livewire\Access;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use Spatie\Permission\Models\Permission as PermissionModel;

#[Title('Akses')]
class Permission extends Component
{
  use AuthorizesRequests, WithPagination;

  public $name;
  public $label;
  public $permissionId;
  public $deleteId;
  public $isEdit = false;

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
    $query = PermissionModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('name', 'like', "%{$this->search}%")
            ->orWhere('label', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('name', 'ASC');

    return view('livewire.access.permission', [
      'data'        => $query->paginate(10),
      'title'       => 'Akses',
      'hasActions'  => auth()->user()->canAny([
        'permission.edit',
        'permission.delete',
        'permission.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('permission.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('permission.edit');

    $this->resetValidation();
    $permission = PermissionModel::find($id);

    if (!$permission) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->name         = $permission->name;
    $this->label        = $permission->label;
    $this->permissionId = $id;
    $this->isEdit       = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->validate(
      [
        'name'  => ['required', 'min:3', \Illuminate\Validation\Rule::unique('permissions', 'name')->ignore($this->permissionId)],
        'label' => ['required', 'min:6', \Illuminate\Validation\Rule::unique('permissions', 'label')->ignore($this->permissionId)],
      ],
      [
        'name.required'   => 'Nama Akses wajib diisi.',
        'name.min'        => 'Nama Akses minimal 3 karakter.',
        'name.unique'     => 'Nama Akses sudah digunakan.',
        'label.required'  => 'Label Akses wajib diisi.',
        'label.min'       => 'Label Akses minimal 6 karakter.',
        'label.unique'    => 'Label Akses sudah digunakan.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('permission.create');

    try {
      PermissionModel::create([
        'name'        => strtolower(trim($this->name)),
        'label'       => strtolower(trim($this->label)),
        'guard_name'  => 'web',
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
    $this->authorize('permission.edit');

    try {
      PermissionModel::find($this->permissionId)->update([
        'name'  => strtolower(trim($this->name)),
        'label' => strtolower(trim($this->label)),
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
    $this->authorize('permission.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('permission.delete');

    if ($this->deleteId) {
      try {
        PermissionModel::destroy($this->deleteId);
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
    $this->name         = '';
    $this->label        = '';
    $this->permissionId = null;
    $this->deleteId     = null;
  }
}
