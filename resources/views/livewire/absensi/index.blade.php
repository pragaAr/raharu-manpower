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
              <th class="fs-5">Ket</th>
              <th class="fs-5">Status</th>
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
              <td class="text-uppercase">{{ $row->lastLog->keterangan ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->lastLog?->jenis ?? '-'}}</td>
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
              <td colspan="{{ $hasActions ? 9 : 8 }}" class="text-center text-muted">Belum ada data.</td>
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
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm]);

    window.__plugins.absensi =
      createTomSelectGroup('#absensi-form', [
        {
          selectId: 'absensi-karyawanSelect',
          errorId: 'absensi-karyawanError',
          hiddenInputId: 'absensi-karyawanHidden',
          placeholder: 'Pilih Karyawan..'
        }
      ])
  })

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.absensi?.destroy()
    delete window.__plugins.absensi
  })

  Livewire.on('openModal', (payload = {}) => {
    toggleModal('addEditModal', 'show');

    const ts = window.__plugins.absensi;
    if (!ts) return;

    if (payload.karyawan_id) {
      ts.clear('absensi-karyawanSelect');    
      ts.setValue('absensi-karyawanSelect', payload.karyawan_id); 
    } else {
      ts.clear('absensi-karyawanSelect');
    }
  });

  Livewire.on('closeModal', () => {
    toggleModal('addEditModal', 'hide');
    window.__plugins.karyawanSelect?.reset();
  });

  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide')); 

  Livewire.on('reset-select', () => {
    window.__plugins.absensi?.reset()
  })
</script>
@endpush