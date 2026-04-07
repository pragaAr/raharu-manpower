<div>

  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'unit.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text" class="form-control"
          id="search-unit"
          placeholder="Cari unit..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead>
            <tr>
              <th class="fs-5 text-center" style="width:8%">#</th>
              <th class="fs-5">Divisi</th>
              <th class="fs-5">Nama</th>
              <th class="fs-5">Kode</th>
              @if($hasActions)
              <th class="fs-5 text-center">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td class="text-uppercase">{{ $row->divisi->nama ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->nama }}</td>
              <td class="text-uppercase">{{ $row->kode }}</td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('unit.edit')
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
                  @can('unit.delete')
                  <button wire:click="confirmDelete({{ $row->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $row->id }})" class="btn btn-danger btn-sm">
                    <span wire:loading wire:target="confirmDelete({{ $row->id }})" title="Hapus" class="spinner-border spinner-border-sm"></span>
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
              <td colspan="5" class="text-center text-muted">Belum ada data.</td>
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
  @include('livewire.master.unit.addEdit-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm]);

    window.__plugins.unit =
      createTomSelectGroup('#unit-form', [
        {
          selectId: 'divisi-select',
          errorId: 'divisi-error',
          hiddenInputId: 'divisi-hidden',
          placeholder: 'Pilih Divisi..'
        }
      ]);
  })

  Livewire.on('openModal', (payload = {}) => {
    toggleModal('addEditModal', 'show');

    const ts = window.__plugins.unit;
    if (!ts) return;

    if (payload.divisi_id) {
      ts.clear('divisi-select');    
      ts.setValue('divisi-select', payload.divisi_id); 
    } else {
      ts.clear('divisi-select');
    }
  });

  Livewire.on('closeModal', () => {
    toggleModal('addEditModal', 'hide');
    window.__plugins.unit?.reset();
  });

  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.unit?.destroy()
    delete window.__plugins.unit
  })
</script>
@endpush