<div>

  @include('components.partials.header', ['title' => $title, 'permission' => 'pengajuan-double-shift.create'])

  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text"
          id="search-pengajuan-double-shift"
          class="form-control"
          placeholder="Cari pengajuan double shift..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead>
            <tr>
              <th class="fs-5 text-center" style="width:8%">#</th>
              <th class="fs-5">Tanggal</th>
              <th class="fs-5">Karyawan</th>
              <th class="fs-5 text-center">Shift Awal</th>
              <th class="fs-5 text-center">Shift Tambahan</th>
              <th class="fs-5 text-center">Status</th>
              <th class="fs-5">Catatan</th>
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
              <td class="text-center text-uppercase">
                {{ $row->shiftAwal->nama ?? '-' }}
                <small class="d-block text-muted">
                  {{ $row->shiftAwal->jam_masuk?->format('H:i') ?? '-' }} - {{ $row->shiftAwal->jam_pulang?->format('H:i') ?? '-' }}
                </small>
              </td>
              <td class="text-center text-uppercase">
                {{ $row->shiftTambahan->nama ?? '-' }}
                <small class="d-block text-muted">
                  {{ $row->shiftTambahan->jam_masuk?->format('H:i') ?? '-' }} - {{ $row->shiftTambahan->jam_pulang?->format('H:i') ?? '-' }}
                </small>
              </td>
              <td class="text-center text-uppercase">{{ $row->status }}</td>
              <td class="text-uppercase">{{ $row->catatan ?? '-' }}</td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('pengajuan-double-shift.edit')
                  <button wire:click="edit({{ $row->id }})" wire:loading.attr="disabled"
                    wire:target="edit({{ $row->id }})" title="Edit" class="btn btn-warning btn-sm">
                    <span wire:loading wire:target="edit({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="edit({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                      <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                      <path d="M16 5l3 3" />
                    </svg>
                  </button>
                  @endcan
                  @can('pengajuan-double-shift.delete')
                  <button wire:click="confirmDelete({{ $row->id }})" wire:loading.attr="disabled"
                    wire:target="confirmDelete({{ $row->id }})" title="Hapus" class="btn btn-danger btn-sm">
                    <span wire:loading wire:target="confirmDelete({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="confirmDelete({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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

  @include('livewire.pengajuan.double-shift-modal')
  @include('components.modal.confirm')
</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');
    blurActiveElementOnModalHide([modalEl, modalConfirm]);
  });

  Livewire.on('openModal', () => toggleModal('addEditModal', 'show'));
  Livewire.on('closeModal', () => toggleModal('addEditModal', 'hide'));
  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));
</script>
@endpush
