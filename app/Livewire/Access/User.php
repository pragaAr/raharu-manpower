<?php

namespace App\Livewire\Access;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\{
  Hash,
  DB,
  Log
};

use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use Spatie\Permission\Models\{
  Role,
  Permission
};

use App\Models\{
  User as UserModel,
  Karyawan
};

#[Title('User')]
class User extends Component
{
  use AuthorizesRequests, WithPagination;

  public $username;
  public $password;
  public $role_id;
  public $karyawan_id;
  public $userId;
  public $deleteId;

  public bool $showCustomPermissions = false;

  public array $rolePermissions     = [];
  public array $customPermissions   = [];
  public array $selectedPermissions = [];

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

  public function updatedRoleId($value)
  {
    if (!$value) {
      $this->rolePermissions = [];
      $this->showCustomPermissions = false;
      return;
    }

    $this->fetchRolePermissions($value);
    $this->showCustomPermissions = true;

    // bersihin selected permission yang ternyata milik role
    $this->selectedPermissions = array_values(array_diff(
      $this->selectedPermissions,
      array_map('strval', $this->rolePermissions)
    ));
  }

  private function fetchRolePermissions($roleId)
  {
    if ($roleId) {
      $this->rolePermissions = Role::find($roleId)?->permissions->pluck('id')->toArray() ?? [];
    } else {
      $this->rolePermissions = [];
    }
  }

  public function render()
  {
    $query = UserModel::with(['roles', 'karyawan'])
      ->where(function ($q) {
        $q->where('username', 'like', '%' . $this->search . '%')
          ->orWhereHas('karyawan', function ($sub) {
            $sub->where('nama', 'like', '%' . $this->search . '%');
          });
      })
      ->whereDoesntHave('roles', function ($q) {
        $q->where('name', 'Superuser');
      })
      ->orderBy('id', 'ASC');

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

    return view('livewire.access.user', [
      'data'                => $query->paginate(10),
      'roles'               => Role::where('name', '!=', 'Superuser')->get(),
      'karyawans'           => Karyawan::orderBy('nama')->get(),
      'groupedPermissions'  => $groupedPermissions,
      'title'               => 'User',
      'hasActions'          => auth()->user()->canAny([
        'user.edit',
        'user.delete',
        'user.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('user.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->showCustomPermissions = false;

    $this->dispatch('openModal', karyawan_id: null, role_id: null);
  }

  public function edit($id)
  {
    $this->authorize('user.edit');

    $this->resetValidation();
    $user = UserModel::with('permissions')->findOrFail($id);

    if (!$user) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->userId               = $id;
    $this->username             = $user->username;
    $this->role_id              = $user->roles->first()?->id;
    $this->karyawan_id          = $user->karyawan_id;
    $this->selectedPermissions  = $user->permissions
      ->pluck('id')
      ->map(fn($id) => (string)$id)
      ->toArray();

    $this->fetchRolePermissions($this->role_id);
    $this->showCustomPermissions  = true;
    $this->isEdit                 = true;

    $this->dispatch('openModal', karyawan_id: $this->karyawan_id, role_id: $this->role_id);
  }

  public function save()
  {
    $this->validate(
      [
        'username'    => ['required', 'min:3', Rule::unique('user', 'username')->ignore($this->userId)],
        'role_id'     => ['required'],
        'karyawan_id' => ['required'],
      ],
      [
        'username.required'     => 'Username wajib diisi.',
        'username.min'          => 'Username minimal 3 karakter.',
        'username.unique'       => 'Username sudah digunakan.',
        'role_id.required'      => 'Role wajib diisi.',
        'karyawan_id.required'  => 'Karyawan wajib diisi.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('user.create');

    try {
      DB::transaction(function () {
        $user = UserModel::create([
          'username'    => $this->username,
          'karyawan_id' => $this->karyawan_id,
          'password'    => Hash::make('h12345678_'),
        ]);

        $user->syncRoles([(int)$this->role_id]);
        $user->syncPermissions(array_map('intval', $this->selectedPermissions));
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);
      return true;
    } catch (\Exception $e) {
      Log::error('User creation failed: ' . $e->getMessage(), [
        'username'    => $this->username,
        'role_id'     => $this->role_id,
        'karyawan_id' => $this->karyawan_id,
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
    $this->authorize('user.edit');

    try {
      DB::transaction(function () {
        $user = UserModel::find($this->userId);

        $user->update([
          'username'    => $this->username,
          'karyawan_id' => $this->karyawan_id,
        ]);

        $user->syncRoles([(int)$this->role_id]);
        $user->syncPermissions(array_map('intval', $this->selectedPermissions));
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);
      return true;
    } catch (\Exception $e) {
      Log::error('User update failed: ' . $e->getMessage(), [
        'id'          => $this->userId,
        'username'    => $this->username,
        'role_id'     => $this->role_id,
        'karyawan_id' => $this->karyawan_id,
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

  public function resetPassword($id)
  {
    $this->authorize('user.edit');

    try {
      $user = UserModel::find($id);
      if (!$user) {
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'User tidak ditemukan.'
        ]);
        return;
      }

      $user->update(['password' => Hash::make('h12345678_')]);

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Password berhasil direset ke default.'
      ]);
    } catch (\Exception $e) {
      Log::error('Password reset failed: ' . $e->getMessage(), [
        'id'        => $id,
        'exception' => $e
      ]);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Password gagal direset.'
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('user.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('user.delete');

    if ($this->deleteId) {
      try {
        UserModel::destroy($this->deleteId);
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
    $this->username             = '';
    $this->password             = '';
    $this->role_id              = '';
    $this->karyawan_id          = '';
    $this->userId               = null;
    $this->deleteId             = null;
    $this->selectedPermissions  = [];
    $this->rolePermissions      = [];
  }
}
