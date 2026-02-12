<div>

  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'jabatan.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text" class="form-control"
          placeholder="Cari jabatan..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead>
            <tr>
              <th class="fs-5 text-center" style="width:8%">#</th>
              <th class="fs-5">Unit</th>
              <th class="fs-5">Jabatan</th>
              @if($hasActions)
              <th class="fs-5 text-center">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td class="text-uppercase">{{ $row->unit->nama ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->nama }}</td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('jabatan.edit')
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
                  @can('jabatan.delete')
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
              <td colspan="4" class="text-center text-muted">Belum ada data.</td>
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
  @include('livewire.master.jabatan-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    const selectEl = document.getElementById('unit-select')
    const hiddenInput = document.getElementById('unit-hidden')
    const errorContainer = document.getElementById('unit-error')
    bindTomSelectModalValidation({ modalEl: modalEl, selectEl, errorEl: errorContainer});

    if (selectEl) {
      if (selectEl.tomselect) {
        selectEl.tomselect.destroy();
      }

     let unitSelect = new TomSelect(selectEl, {
        create: false,
        allowEmptyOption: true,
        placeholder: 'Pilih Unit..',
        items: [],
        onChange(value) {
          // Set hidden input value dan trigger input event untuk Livewire
          hiddenInput.value = value
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }))
        }
      })

      // Prevent Enter key from submitting form
      unitSelect.control_input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault()
          e.stopPropagation()
        }
      })

      Livewire.on('openModal', (payload = {}) => {
        toggleModal('addEditModal', 'show');

        // Reset active option to prevent sticky state
        if (unitSelect.activeOption) {
          unitSelect.setActiveOption(null)
        }

        if (payload.unit_id) {
          unitSelect.setValue(payload.unit_id, true)
        } else {
          unitSelect.clear(true)
        }

        // Force refresh to ensure UI is consistent
        unitSelect.refreshOptions(false)
      });

      Livewire.on('closeModal', () => {
        toggleModal('addEditModal', 'hide');

        unitSelect.clear(true)

        if (unitSelect.activeOption) {
          unitSelect.setActiveOption(null)
        }
      });
    }

    Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
    Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

    blurActiveElementOnModalHide([modalEl, modalConfirm]);
  })
 
</script>
@endpush