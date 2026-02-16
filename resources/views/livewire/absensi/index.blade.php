<div>

 {{-- HEADER PAGE --}}
 @include('components.partials.header', ['title' => $title, 'permission' => 'absensi.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text" class="form-control"
          placeholder="Cari absensi..."
          id="search-absensi"
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead class="text-center">
            <tr>
              <th class="fs-5" style="width:8%">#</th>
              <th class="fs-5">Tanggal</th>
              <th class="fs-5">NIK</th>
              <th class="fs-5">Masuk</th>
              <th class="fs-5">Pulang</th>
              <th class="fs-5">Source</th>
              <th class="fs-5">Input Oleh</th>
              <th class="fs-5">Keterangan</th>
              @if($hasActions)
              <th class="fs-5">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody class="text-center">
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td>{{ $data->firstItem() + $i }}.</td>
              <td>{{ $row->tanggal->format('d-m-Y') }}</td>
              <td class="text-uppercase" title="{{ $row->karyawan->nama }}" style="cursor: help;">{{ $row->karyawan->nik }}</td>
              <td>{{ optional($row->jam_masuk)->format('H:i') ?? '-' }}</td>
              <td>{{ optional($row->jam_pulang)->format('H:i') ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->lastLog->source ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->lastLog?->inputBy?->username ?? '-' }}</td>
              <td>{{ $row->lastLog->keterangan ?? '-' }}</td>
              @if($hasActions)
              <td>
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('absensi.edit')
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
                  @can('absensi.detail')
                  <button wire:click="detail({{ $row->id }})" wire:loading.attr="disabled" wire:target="detail({{ $row->id }})" title="Detail" class="btn btn-info btn-sm">
                    <span wire:loading wire:target="detail({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="detail({{ $row->id }})" xmlns="http://www.w3.org/2000/svg" 
                      style="width: 18px; height: 18px;" 
                      viewBox="0 0 24 24" 
                      fill="none" 
                      stroke="currentColor" 
                      stroke-width="2" 
                      stroke-linecap="round" 
                      stroke-linejoin="round" 
                      class="icon icon-tabler icons-tabler-outline icon-tabler-eye me-0">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                  </button>
                  @endcan
                  @can('absensi.delete')
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
              <td colspan="9" class="text-center text-muted">Belum ada data.</td>
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
  @include('livewire.absensi.addEdit-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    const dateFields = ['tanggal', 'masuk', 'pulang'];

    dateFields.forEach((id) => {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('click', function() {
          if (typeof this.showPicker === 'function') {
            try {
              this.showPicker();
            } catch (e) {
              console.warn("Gagal memanggil picker:", e);
            }
          }
        });
      }
    });

    const selectEl = document.getElementById('karyawan-select');
    const errorContainer = document.getElementById('karyawan-error');

    bindTomSelectModalValidation({ modalEl: modalEl, selectEl, errorEl: errorContainer});

    if (selectEl) {
      if (selectEl.tomselect) {
        selectEl.tomselect.destroy();
      }

      let karyawanSelect = new TomSelect(selectEl, {
        create: false,
        allowEmptyOption: true,
        placeholder: 'Pilih Karyawan..',
        items: [],
        onChange(value) {
          hiddenInput.value = value
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }))
        }
      })
      
      karyawanSelect.control_input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault()
          e.stopPropagation()
        }
      })

      Livewire.on('openModal', (payload = {}) => {
        toggleModal('addEditModal', 'show');

        if (karyawanSelect.activeOption) {
          karyawanSelect.setActiveOption(null)
        }

        if (payload.karyawan_id) {
          karyawanSelect.setValue(payload.karyawan_id, true)
        } else {
          karyawanSelect.clear(true)
        }

        karyawanSelect.refreshOptions(false)
      });

      Livewire.on('closeModal', () => {
        toggleModal('addEditModal', 'hide');
        karyawanSelect.clear(true)

        if (karyawanSelect.activeOption) {
          karyawanSelect.setActiveOption(null)
        }
      });
    }

    Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
    Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

    document.addEventListener('resetSelect', () => {
      document.querySelectorAll('select').forEach(select => {
        if (select.tomselect) {
          select.tomselect.clear()
        }
      })
    });   

    blurActiveElementOnModalHide([modalEl, modalConfirm]);
  })
</script>
@endpush