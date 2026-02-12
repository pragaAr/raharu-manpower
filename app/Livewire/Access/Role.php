<?php

namespace App\Livewire\Access;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\{
  DB,
  Log
};

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use Spatie\Permission\Models\{
  Role as RoleModel,
  Permission
};


#[Title('Role')]
class Role extends Component
{
  use AuthorizesRequests, WithPagination;

  public $name;
  public $roleId;
  public $deleteId;
  public $selectedPermissions = [];
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

  public function updatedName($value)
  {
    if (strtolower($value) === 'administrator') {
      $this->selectedPermissions = Permission::pluck('id')->map(fn($id) => (string)$id)->toArray();
    }
  }

  public function updatedSelectedPermissions($value)
  {
    $allPermissions = Permission::all();
    $newSelected    = $this->selectedPermissions;
    $changed        = false;

    foreach ($newSelected as $id) {
      $p = $allPermissions->find($id);
      if (!$p) continue;

      $parts = explode('.', $p->name);
      if (count($parts) > 1) {
        $module = $parts[0];
        $action = $parts[1];

        // If ANY action besides view/read is selected, auto-select view/read
        if (!in_array($action, ['view', 'read'])) {
          $readP = $allPermissions->where('name', $module . '.view')->first()
            ?? $allPermissions->where('name', $module . '.read')->first();

          if ($readP && !in_array((string)$readP->id, $newSelected)) {
            $newSelected[] = (string)$readP->id;
            $changed = true;
          }
        }
      }
    }

    if ($changed) {
      $this->selectedPermissions = array_values(array_unique($newSelected));
    }
  }

  public function render()
  {
    $query = RoleModel::query()
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('name', 'like', "%{$this->search}%");
        });
      })
      ->orderBy('name', 'ASC')
      ->where('name', '!=', 'Superuser');

    // Group permissions by prefix (e.g. 'karyawan.view' -> 'karyawan')
    $groupedPermissions = Permission::all()->groupBy(function ($permission) {
      $parts = explode('.', $permission->name);
      return count($parts) > 1 ? ucfirst($parts[0]) : 'General';
    })->map(function ($permissions) {
      // Sort permissions by action: read, add, edit, delete
      return $permissions->sortBy(function ($permission) {
        $parts = explode('.', $permission->name);
        $action = count($parts) > 1 ? $parts[1] : $permission->name;

        // Define sort order
        $order = [
          'read'    => 1,
          'view'    => 1,
          'add'     => 2,
          'create'  => 2,
          'edit'    => 3,
          'update'  => 3,
          'delete'  => 4,
          'destroy' => 4
        ];

        return $order[$action] ?? 999;
      });
    });

    return view('livewire.access.role', [
      'data'                => $query->paginate(10),
      'groupedPermissions'  => $groupedPermissions,
      'title'               => 'Role',
      'hasActions'          => auth()->user()->canAny([
        'role.edit',
        'role.delete',
        'role.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('role.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('role.edit');

    $this->resetValidation();
    $role = RoleModel::with('permissions')->find($id);

    if (!$role) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->name                 = $role->name;
    $this->selectedPermissions  = $role->permissions->pluck('id')->map(fn($id) => (string)$id)->toArray();
    $this->roleId               = $id;
    $this->isEdit               = true;

    $this->dispatch('openModal');
  }

  public function save()
  {
    $this->name = trim($this->name);

    $this->validate(
      [
        'name' => ['required', 'min:3', \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($this->roleId)],
      ],
      [
        'name.required' => 'Nama Role wajib diisi.',
        'name.min'      => 'Nama Role minimal 3 karakter.',
        'name.unique'   => 'Nama Role sudah digunakan.',
      ]
    );

    $success = $this->isEdit ? $this->updateData() : $this->storeData();

    if ($success) {
      $this->resetForm();
      $this->dispatch('closeModal');
    }
  }

  public function storeData()
  {
    $this->authorize('role.create');

    try {
      DB::transaction(function () {
        $role = RoleModel::create([
          'name'        => $this->name,
          'guard_name'  => 'web',
        ]);

        if (strtolower($this->name) === 'administrator') {
          $this->selectedPermissions = Permission::pluck('id')->map(fn($id) => (string)$id)->toArray();
        }

        $role->syncPermissions(array_map('intval', $this->selectedPermissions));
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);
      return true;
    } catch (\Exception $e) {
      Log::error('Role creation failed: ' . $e->getMessage(), [
        'name'        => $this->name,
        'permissions' => $this->selectedPermissions,
        'exception'   => $e
      ]);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal ditambah. ' . $e->getMessage()
      ]);
      return false;
    }
  }

  public function updateData()
  {
    $this->authorize('role.edit');

    try {
      DB::transaction(function () {
        $role = RoleModel::find($this->roleId);

        $role->update([
          'name' => $this->name,
        ]);

        if (strtolower($this->name) === 'administrator') {
          $this->selectedPermissions = Permission::pluck('id')->map(fn($id) => (string)$id)->toArray();
        }

        $role->syncPermissions(array_map('intval', $this->selectedPermissions));
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);
      return true;
    } catch (\Exception $e) {
      Log::error('Role update failed: ' . $e->getMessage(), [
        'id'          => $this->roleId,
        'name'        => $this->name,
        'permissions' => $this->selectedPermissions,
        'exception'   => $e
      ]);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal diupdate. ' . $e->getMessage()
      ]);
      return false;
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('role.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('role.delete');

    if ($this->deleteId) {
      try {
        RoleModel::destroy($this->deleteId);
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
    $this->name                 = '';
    $this->roleId               = null;
    $this->deleteId             = null;
    $this->selectedPermissions  = [];
  }
}
