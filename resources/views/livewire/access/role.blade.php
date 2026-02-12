<div>

  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'role.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text" class="form-control"
          placeholder="Cari role..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered mb-2">
          <thead class="text-center">
            <tr>
              <th class="fs-5">#</th>
              <th class="fs-5">Nama Role</th>
              <th class="fs-5">Permissions</th>
              @if($hasActions)
              <th class="fs-5">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td class="text-uppercase">{{ $row->name }}</td>
              <td>
                @foreach($row->permissions->take(5) as $p)
                <span class="badge badge-outline text-lime text-capitalize mb-1 me-1">{{ $p->label ?: $p->name }}</span>
                @endforeach

                @if($row->permissions->count() > 5)
                <span class="badge badge-outline text-secondary mb-1 me-1" 
                      title="{{ $row->permissions->skip(5)->map(fn($p) => $p->label ?: $p->name)->implode(', ') }}"
                      style="cursor: help;">
                  +{{ $row->permissions->count() - 5 }} lainnya
                </span>
                @endif
              </td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('role.edit')
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
                  @endcan
                  @can('role.delete')
                  @if($row->name !== 'Administrator')
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
                  @endif
                  @endcan
                </div>
              </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="{{ $hasActions ? 4 : 3 }}" class="text-center text-muted">Belum ada data.</td>
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

  {{-- MODAL --}}
  @include('livewire.access.role-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    Livewire.on('openModal', () => toggleModal('addEditModal', 'show'));
    Livewire.on('closeModal', () => toggleModal('addEditModal', 'hide'));

    Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
    Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

    blurActiveElementOnModalHide([modalEl, modalConfirm]);
  });
</script>
@endpush
