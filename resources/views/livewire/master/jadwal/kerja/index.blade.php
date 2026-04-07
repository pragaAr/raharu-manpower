<div>

  {{-- HEADER PAGE --}}
  @php
    ob_start();
  @endphp
  @can('jadwal-kerja.create')
  <button type="button"
    wire:click="openGenerateModal"
    wire:loading.attr="disabled"
    wire:target="openGenerateModal"
    class="btn btn-success btn-sm-custom">

    <svg 
      wire:loading.remove 
      wire:target="openGenerateModal" 
      xmlns="http://www.w3.org/2000/svg" 
      class="icon m-0" 
      width="24" height="24" 
      viewBox="0 0 24 24" 
      stroke-width="2" 
      stroke="currentColor" 
      fill="none" 
      stroke-linecap="round" 
      stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
      <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
    </svg>

    <span
      wire:loading
      wire:target="openGenerateModal"
      class="spinner-border spinner-custom">
    </span>
    
    <span class="d-none d-sm-inline ms-1">Generate Jadwal</span>
  </button>
  @endcan
  @php
    $extraButtons = ob_get_clean();
  @endphp
  @include('components.partials.header', [
    'title' => $title,
    'permission' => 'jadwal-kerja.create',
    'extraButtons' => $extraButtons
  ])

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      <div class="row g-2 mb-3">
        <div class="col-12 col-md-6">
          <input type="text"
            id="search-jadwal-kerja"
            class="form-control"
            placeholder="Cari jadwal kerja..."
            wire:model.live.debounce.300ms="search">
        </div>
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-md-end">
          <div class="btn-group" role="group" aria-label="Filter jadwal kerja">
            <input type="radio" class="btn-check" name="jadwal-view" id="jadwal-view-all"
              value="all" wire:model.live="viewMode">
            <label class="btn btn-outline-primary btn-sm" for="jadwal-view-all">Semua</label>

            <input type="radio" class="btn-check" name="jadwal-view" id="jadwal-view-rolling"
              value="rolling" wire:model.live="viewMode">
            <label class="btn btn-outline-primary btn-sm" for="jadwal-view-rolling">Rolling/Bulanan</label>

            <input type="radio" class="btn-check" name="jadwal-view" id="jadwal-view-fixed"
              value="fixed" wire:model.live="viewMode">
            <label class="btn btn-outline-primary btn-sm" for="jadwal-view-fixed">Fixed</label>
          </div>
        </div>
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
  @include('livewire.master.jadwal.kerja.addEdit-modal')
  
  <div wire:ignore.self class="modal fade" id="generateModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog {{ $previewReady ? 'modal-xl' : 'modal-sm' }}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Generate Jadwal Kerja
          </h5>
          <button type="button" wire:click="cancelGenerateJadwal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <form id="generate-jadwal-form">
            <div class="mt-2">
              <label for="generate-month" class="form-label">Bulan</label>
              <input type="month"
                id="generate-month"
                wire:model.live="generateMonth"
                class="form-control @error('generateMonth') is-invalid @enderror">
              @error('generateMonth')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mt-2">
              <label for="generate-lokasiSelect" class="form-label">Lokasi</label>
              <input type="hidden" id="generate-lokasiHidden" wire:model.live="generateLokasiId">
              <div wire:ignore>
                <select id="generate-lokasiSelect" class="form-select">
                  <option value="">Pilih Lokasi</option>
                  @foreach($lokasis as $lokasi)
                  <option value="{{ $lokasi->id }}">{{ strtoupper($lokasi->nama) }}</option>
                  @endforeach
                </select>
              </div>
              <div id="generate-lokasiError">
                @error('generateLokasiId')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </form>

          <div class="mt-3 text-center">
            <button type="button"
              wire:click="previewGenerateJadwalSatpam"
              wire:loading.attr="disabled"
              wire:target="previewGenerateJadwalSatpam"
              class="btn btn-primary w-100">
              Preview
              <span wire:loading wire:target="previewGenerateJadwalSatpam" class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>

          @if($previewReady)
          <hr class="my-3">
          <div class="text-center fw-semibold mb-2">Preview</div>

          @if(!empty($previewMinLiburByLokasi))
          <div class="mb-3">
            <div class="fw-semibold mb-2">Kuota Libur Per Lokasi</div>
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead>
                  <tr>
                    <th>Lokasi</th>
                    <th class="text-center">Jumlah Satpam</th>
                    <th class="text-center">Kuota Libur</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($previewMinLiburByLokasi as $row)
                  <tr>
                    <td class="text-uppercase">{{ $row['lokasi'] ?? '-' }}</td>
                    <td class="text-center">{{ $row['jumlah_satpam'] ?? 0 }}</td>
                    <td class="text-center">{{ $row['min'] ?? 0 }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          @endif

          <div class="row g-2 mb-3">
            <div class="col-4">
              <div class="border rounded p-2 text-center">
                <div class="text-muted">Dibuat</div>
                <div class="fs-4">{{ $previewSummary['created'] ?? 0 }}</div>
              </div>
            </div>
            <div class="col-4">
              <div class="border rounded p-2 text-center">
                <div class="text-muted">Diupdate</div>
                <div class="fs-4">{{ $previewSummary['updated'] ?? 0 }}</div>
              </div>
            </div>
            <div class="col-4">
              <div class="border rounded p-2 text-center">
                <div class="text-muted">Dilewati</div>
                <div class="fs-4">{{ $previewSummary['skipped'] ?? 0 }}</div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <div class="fw-semibold mb-2">Libur Per Satpam</div>
              <div class="table-responsive" style="max-height: 240px;">
                <table class="table table-sm table-bordered">
                  <thead>
                    <tr>
                      <th>Lokasi</th>
                      <th>Nama</th>
                      <th>Tgl Libur</th>
                      <th class="text-center">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($previewOffBySatpam as $row)
                    <tr>
                      <td class="text-uppercase">{{ $row['lokasi'] ?? '-' }}</td>
                      <td class="text-uppercase">{{ $row['satpam'] ?? '-' }}</td>
                      <td>{{ !empty($row['dates']) ? implode(', ', $row['dates']) : '-' }}</td>
                      <td class="text-center">{{ !empty($row['dates']) ? count($row['dates']) : 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted">Tidak ada libur.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-12">
              <div class="fw-semibold mb-2">Jadwal Kerja (Grid)</div>
              <div class="table-responsive" style="max-height: 320px;">
                <table class="table table-sm table-bordered">
                  <thead>
                    <tr>
                      <th>Lokasi</th>
                      <th>Nama</th>
                      @foreach($previewGridDays as $day)
                      <th class="text-center" style="width: 34px;">{{ $day }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($previewJadwalGrid as $row)
                    <tr>
                      <td class="text-uppercase">{{ $row['lokasi'] ?? '-' }}</td>
                      <td class="text-uppercase">{{ $row['satpam'] ?? '-' }}</td>
                      @foreach($previewGridDays as $day)
                      @php
                        $code = $row['grid'][$day] ?? '-';
                        $badgeClass = match ($code) {
                          'P' => 'bg-green-lt',
                          'S' => 'bg-yellow-lt',
                          'M' => 'bg-indigo-lt',
                          'L' => 'bg-red-lt',
                          default => 'bg-secondary-lt',
                        };
                      @endphp
                      <td class="text-center">
                        @if($code !== '-')
                        <span class="badge {{ $badgeClass }}">{{ $code }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                      </td>
                      @endforeach
                    </tr>
                    @empty
                    <tr>
                      <td colspan="{{ 2 + count($previewGridDays) }}" class="text-center text-muted">Belum ada data.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <small class="text-muted d-block mt-1">
                Keterangan:
                <span class="badge bg-green-lt">P</span> Pagi
                <span class="badge bg-yellow-lt ms-1">S</span> Sore
                <span class="badge bg-indigo-lt ms-1">M</span> Malam
                <span class="badge bg-red-lt ms-1">L</span> Libur
              </small>
            </div>
          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" wire:click="cancelGenerateJadwal" data-bs-dismiss="modal">Batal</button>
            <button type="button"
              class="btn btn-success"
              wire:click="saveGenerateJadwalSatpam"
              wire:loading.attr="disabled"
              wire:target="saveGenerateJadwalSatpam"
              @if(!$previewReady) disabled @endif>
              Simpan
              <span wire:loading wire:target="saveGenerateJadwalSatpam" class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>
          @endif

        </div>
      
      </div>
    </div>
  </div>

  @include('components.modal.confirm')

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    const modalEl = document.getElementById('addEditModal');
    const modalConfirm = document.getElementById('confirmModal');
    const modalGenerate = document.getElementById('generateModal');

    blurActiveElementOnModalHide([modalEl, modalConfirm, modalGenerate]);

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

    window.__plugins.jadwalKerjaGenerate =
      createTomSelectGroup('#generate-jadwal-form', [
        {
          selectId: 'generate-lokasiSelect',
          errorId: 'generate-lokasiError',
          hiddenInputId: 'generate-lokasiHidden',
          placeholder: 'Pilih Lokasi..'
        }
      ])
  })

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.jadwalKerja?.destroy()
    delete window.__plugins.jadwalKerja

    window.__plugins.jadwalKerjaGenerate?.destroy()
    delete window.__plugins.jadwalKerjaGenerate
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

  Livewire.on('openGenerateModal', (payload = {}) => {
    toggleModal('generateModal', 'show');

    const ts = window.__plugins.jadwalKerjaGenerate;
    if (!ts) return;

    if (Array.isArray(payload.lokasi_options)) {
      ts.refresh('generate-lokasiSelect', payload.lokasi_options, (item) => {
        return String(item.nama ?? '').toUpperCase();
      });
    }

    if (payload.lokasi_id) {
      ts.clear('generate-lokasiSelect');
      ts.setValue('generate-lokasiSelect', payload.lokasi_id);
    } else {
      ts.clear('generate-lokasiSelect');
    }
  });
  Livewire.on('closeGenerateModal', () => toggleModal('generateModal', 'hide'));
</script>
@endpush
