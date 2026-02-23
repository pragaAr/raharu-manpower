<div>
  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'user.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text" class="form-control" 
          placeholder="Cari user..." 
          id="search-user" 
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered mb-2">
          <thead class="text-center">
            <tr>
              <th class="fs-5" style="width:8%">#</th>
              <th class="fs-5">Username</th>
              <th class="fs-5">Nama Karyawan</th>
              <th class="fs-5">Role</th>
              @if($hasActions)
              <th class="fs-5">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td>{{ $data->firstItem() + $i }}.</td>
              <td>{{ $row->username }}</td>
              <td class="text-capitalize">
                {{ $row->karyawan->nama ?? '-' }}
              </td>
              <td>
                @foreach($row->roles as $role)
                <span class="badge badge-outline text-lime text-capitalize">
                  {{ $role->name }}
                </span>
                @endforeach
              </td>
              @if($hasActions)
              <td>
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('user.edit')
                  <button wire:click="edit({{ $row->id }})" wire:loading.attr="disabled" wire:target="edit({{ $row->id }})" title="Edit" class="btn btn-warning btn-sm">
                    <span wire:loading wire:target="edit({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="edit({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                      <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                      <path d="M16 5l3 3" />
                    </svg>
                  </button>
                  <button wire:click="resetPassword({{ $row->id }})" wire:loading.attr="disabled" wire:target="resetPassword({{ $row->id }})" title="Reset Password" class="btn btn-info btn-sm">
                    <span wire:loading wire:target="resetPassword({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="resetPassword({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-key me-0">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0z" />
                      <path d="M15 9h.01" />
                    </svg>
                  </button>
                  @endcan
                  @can('user.delete')
                  <button wire:click="confirmDelete({{ $row->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $row->id }})" title="Hapus" class="btn btn-danger btn-sm">
                    <span wire:loading wire:target="confirmDelete({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="confirmDelete({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-0">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M4 7l16 0" />
                      <path d="M10 11l0 6" />
                      <path d="M14 11l0 6" />
                      <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                      <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg>
                  </button>
                  @endcan
                </div>
              </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="{{ $hasActions ? 5 : 4 }}" class="text-center text-muted">Belum ada data.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 px-2">
        {{ $data->links() }}
      </div>

    </div>
  </div>

  {{-- Modal --}}
  @include('livewire.access.user-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm]);

    window.__plugins.user =
      createTomSelectGroup('#user-form', [
        {
          selectId: 'karyawan-select',
          errorId: 'karyawan-error',
          hiddenInputId: 'karyawan-hidden',
          placeholder: 'Pilih Karyawan..'
        },
        {
          selectId: 'role-select',
          errorId: 'role-error',
          hiddenInputId: 'role-hidden',
          placeholder: 'Pilih Role..'
        }
      ]);
  });

  Livewire.on('openModal', (payload = {}) => {
    toggleModal('addEditModal', 'show');

    const ts = window.__plugins.user;
    if (!ts) return;

    if (payload.karyawan_id) {
      ts.clear('karyawan-select');    
      ts.setValue('karyawan-select', payload.karyawan_id); 
    } else {
      ts.clear('karyawan-select');
    }
    
    if (payload.role_id) {
      ts.clear('role-select');    
      ts.setValue('role-select', payload.role_id); 
    } else {
      ts.clear('role-select');
    }
  });

  Livewire.on('closeModal', () => {
    toggleModal('addEditModal', 'hide');
    window.__plugins.user?.reset();
  });

  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.user?.destroy()
    delete window.__plugins.user
  })
</script>
@endpush
