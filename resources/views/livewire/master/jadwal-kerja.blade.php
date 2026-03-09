<div>

  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'jadwal-kerja.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text"
          id="search-jadwal-kerja"
          class="form-control"
          placeholder="Cari jadwal kerja..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead>
            <tr>
              <th class="fs-5 text-center" style="width:8%">#</th>
              <th class="fs-5">Tanggal</th>
              <th class="fs-5">Karyawan</th>
              <th class="fs-5">Shift</th>
              <th class="fs-5 text-center">Jam</th>
              <th class="fs-5 text-center">Flag</th>
              <th class="fs-5 text-center">Source</th>
              @if($hasActions)
              <th class="fs-5 text-center">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td>{{ $row->tanggal?->format('d-m-Y') ?? '-' }}</td>
              <td class="text-uppercase">
                <div class="fw-semibold">{{ $row->karyawan->nik ?? '-' }}</div>
                <div class="text-muted">{{ $row->karyawan->nama ?? '-' }}</div>
              </td>
              <td class="text-uppercase">{{ $row->shift->nama ?? $row->shift_nama ?? '-' }}</td>
              <td class="text-center">{{ $row->jam_masuk?->format('H:i') ?? '-' }} - {{ $row->jam_pulang?->format('H:i') ?? '-' }}</td>
              <td class="text-center">
                @if($row->is_libur)
                <span class="badge bg-yellow-lt">LIBUR</span>
                @endif
                @if($row->is_holiday)
                <span class="badge bg-blue-lt">HOLIDAY</span>
                @endif
                @if(!$row->is_libur && !$row->is_holiday)
                <span class="badge bg-secondary-lt">NORMAL</span>
                @endif
              </td>
              <td class="text-center text-uppercase">{{ $row->generated_by ?? '-' }}</td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('jadwal-kerja.edit')
                  <button wire:click="edit({{ $row->id }})" wire:loading.attr="disabled"
                    wire:target="edit({{ $row->id }})" title="Edit" class="btn btn-warning btn-sm">
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
                  @can('jadwal-kerja.delete')
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
              <td colspan="{{ $hasActions ? 8 : 7 }}" class="text-center text-muted">Belum ada data.</td>
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
  @include('livewire.master.jadwal-kerja-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm]);

    window.__plugins.jadwalKerja =
      createTomSelectGroup('#jadwal-kerja-form', [
        {
          selectId: 'jadwal-kerja-karyawanSelect',
          errorId: 'jadwal-kerja-karyawanError',
          hiddenInputId: 'jadwal-kerja-karyawanHidden',
          placeholder: 'Pilih Karyawan..'
        },
        {
          selectId: 'jadwal-kerja-shiftSelect',
          errorId: 'jadwal-kerja-shiftError',
          hiddenInputId: 'jadwal-kerja-shiftHidden',
          placeholder: 'Pilih Shift..'
        }
      ])
  })

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.jadwalKerja?.destroy()
    delete window.__plugins.jadwalKerja
  })

  Livewire.on('openModal', (payload = {}) => {
    toggleModal('addEditModal', 'show');

    const ts = window.__plugins.jadwalKerja;
    if (!ts) return;

    if (Array.isArray(payload.karyawan_options)) {
      ts.refresh('jadwal-kerja-karyawanSelect', payload.karyawan_options, (item) => {
        const nik = String(item.nik ?? '').toUpperCase();
        const nama = String(item.nama ?? '').toUpperCase();
        return [nik, nama].filter(Boolean).join(' - ');
      });
    }

    if (Array.isArray(payload.shift_options)) {
      ts.refresh('jadwal-kerja-shiftSelect', payload.shift_options, (item) => {
        const nama = String(item.nama ?? '').toUpperCase();
        const jamMasuk = item.jam_masuk ?? '--:--';
        const jamPulang = item.jam_pulang ?? '--:--';
        return `${nama} (${jamMasuk} - ${jamPulang})`;
      });
    }

    if (payload.karyawan_id) {
      ts.clear('jadwal-kerja-karyawanSelect');
      ts.setValue('jadwal-kerja-karyawanSelect', payload.karyawan_id);
    } else {
      ts.clear('jadwal-kerja-karyawanSelect');
    }

    if (payload.shift_id) {
      ts.clear('jadwal-kerja-shiftSelect');
      ts.setValue('jadwal-kerja-shiftSelect', payload.shift_id);
    } else {
      ts.clear('jadwal-kerja-shiftSelect');
    }
  });

  Livewire.on('closeModal', () => {
    toggleModal('addEditModal', 'hide');
    window.__plugins.jadwalKerja?.reset();
  });

  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));
</script>
@endpush
