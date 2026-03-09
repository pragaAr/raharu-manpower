<div>

  {{-- HEADER PAGE --}}
  @include('components.partials.header', ['title' => $title, 'permission' => 'work-rule.create'])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="mb-3">
        <input type="text"
          id="search-work-rule"
          class="form-control"
          placeholder="Cari aturan kerja..."
          wire:model.live.debounce.300ms="search">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered">
          <thead>
            <tr>
              <th class="fs-5 text-center" style="width:8%">#</th>
              <th class="fs-5">Jabatan</th>
              <th class="fs-5 text-center">Shift</th>
              <th class="fs-5 text-center">Auto Lembur</th>
              <th class="fs-5 text-center">Approval Lembur</th>
              <th class="fs-5 text-center">Approval Cuti</th>
              <th class="fs-5 text-center">Double Shift</th>
              <th class="fs-5 text-center">Tukar Shift</th>
              @if($hasActions)
              <th class="fs-5 text-center">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td>
                <div class="fw-semibold text-uppercase">{{ $row->jabatan->nama ?? '-' }}</div>
                <div class="text-muted text-uppercase">
                  {{ $row->jabatan->unit->divisi->nama ?? '-' }} - {{ $row->jabatan->unit->nama ?? '-' }}
                </div>
              </td>
              <td class="text-center"><span class="badge {{ $row->use_shift ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $row->use_shift ? 'YA' : 'TIDAK' }}</span></td>
              <td class="text-center"><span class="badge {{ $row->auto_overtime ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $row->auto_overtime ? 'YA' : 'TIDAK' }}</span></td>
              <td class="text-center"><span class="badge {{ $row->overtime_need_approval ? 'bg-warning-lt' : 'bg-success-lt' }}">{{ $row->overtime_need_approval ? 'YA' : 'TIDAK' }}</span></td>
              <td class="text-center"><span class="badge {{ $row->cuti_need_approval ? 'bg-warning-lt' : 'bg-success-lt' }}">{{ $row->cuti_need_approval ? 'YA' : 'TIDAK' }}</span></td>
              <td class="text-center"><span class="badge {{ $row->allow_double_shift ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $row->allow_double_shift ? 'YA' : 'TIDAK' }}</span></td>
              <td class="text-center"><span class="badge {{ $row->allow_shift_swap ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $row->allow_shift_swap ? 'YA' : 'TIDAK' }}</span></td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('work-rule.edit')
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
                  @can('work-rule.delete')
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
  @include('livewire.master.work-rule-modal')
  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm]);

    window.__plugins.workRule =
      createTomSelectGroup('#work-rule-form', [
        {
          selectId: 'jabatan-select',
          errorId: 'jabatan-error',
          hiddenInputId: 'jabatan-hidden',
          placeholder: 'Pilih Jabatan..'
        }
      ]);
  })

  Livewire.on('openModal', (payload = {}) => {
    toggleModal('addEditModal', 'show');

    const ts = window.__plugins.workRule;
    if (!ts) return;

    if (payload.jabatan_id) {
      ts.clear('jabatan-select');
      ts.setValue('jabatan-select', payload.jabatan_id);
    } else {
      ts.clear('jabatan-select');
    }
  });

  Livewire.on('closeModal', () => {
    toggleModal('addEditModal', 'hide');
    window.__plugins.workRule?.reset();
  });

  Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
  Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.workRule?.destroy()
    delete window.__plugins.workRule
  })
</script>
@endpush
