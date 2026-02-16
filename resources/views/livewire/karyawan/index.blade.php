<div>

  {{-- HEADER PAGE --}}
  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Data</div>
        <h2 class="page-title">{{ $title }}</h2>
      </div>

      <div class="col-auto ms-auto d-print-none">
        <button type="button"
          wire:click="openFilter"
          wire:loading.attr="disabled"
          wire:target="openFilter"
          class="btn btn-dark btn-sm-custom">

          <svg
            wire:loading.remove
            wire:target="openFilter"
            xmlns="http://www.w3.org/2000/svg"
            width="24" 
            height="24" 
            viewBox="0 0 24 24"
            fill="none" 
            stroke="currentColor"
            stroke-width="2" 
            stroke-linecap="round"
            stroke-linejoin="round"
            class="icon icon-tabler icon-tabler-sort-ascending m-0">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M4 6l7 0" />
            <path d="M4 12l7 0" />
            <path d="M4 18l9 0" />
            <path d="M15 9l3 -3l3 3" />
            <path d="M18 6l0 12" />
          </svg>

          <span
            wire:loading
            wire:target="openFilter"
            class="spinner-border spinner-custom">
          </span>

          <span class="d-none d-sm-inline ms-1">Filter</span>
        </button>

        @can('karyawan.mutasi')
        <a
          x-data="{ isLoading: false }"
          :href="'{{ route('karyawan.mutasi') }}?back=' + btoa(window.location.href)"
          @click="isLoading = true"
          wire:navigate
          class="btn btn-danger btn-sm-custom">

          <svg 
            x-show="!isLoading"
            xmlns="http://www.w3.org/2000/svg" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-exchange-2 m-0">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M17 10h-14l4 -4" />
              <path d="M7 14h14l-4 4" />
          </svg>

          <span
            x-show="isLoading"
            x-cloak
            class="spinner-border spinner-custom">
          </span>

          <span class="d-none d-sm-inline ms-1">Mutasi</span>
        </a>
        @endcan

        @can('karyawan.renewal')
        <a
          x-data="{ isLoading: false }"
          :href="'{{ route('karyawan.renewal') }}?back=' + btoa(window.location.href)"
          @click="isLoading = true"
          wire:navigate
          class="btn btn-success btn-sm-custom">

          <svg 
            x-show="!isLoading"
            xmlns="http://www.w3.org/2000/svg" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            class="icon icon-tabler icons-tabler-outline icon-tabler-file-plus m-0">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M14 3v4a1 1 0 0 0 1 1h4" />
              <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
              <path d="M12 11l0 6" />
              <path d="M9 14l6 0" />
          </svg>

          <span
            x-show="isLoading"
            x-cloak
            class="spinner-border spinner-custom">
          </span>

          <span class="d-none d-sm-inline ms-1">Renewal</span>
        </a>
        @endcan

        @can('karyawan.create')
        <a
          x-data="{ isLoading: false }"
          :href="'{{ route('karyawan.create') }}?back=' + btoa(window.location.href)"
          @click="isLoading = true"
          wire:navigate
          class="btn btn-primary btn-sm-custom">

          <svg
            x-show="!isLoading"
            xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round"
            class="icon icon-tabler icon-tabler-plus m-0">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
          </svg>

          <span
            x-show="isLoading"
            x-cloak
            class="spinner-border spinner-custom">
          </span>

          <span class="d-none d-sm-inline ms-1">Tambah</span>
        </a>
        @endcan

      </div>

    </div>
  </div>

  {{-- TABLE --}}
  <div class="card">
    <div class="card-body border-bottom py-3">

      @if($displayFilters)
        <div class="text-muted text-capitalize mb-2">
          filter on | 
          @foreach($displayFilters as $label => $value)
            <small> {{ $label }}: {{ $value }}</small>@if(!$loop->last),@endif
          @endforeach
        </div>
      @endif

      <div class="mb-3">
        <input type="text" class="form-control"
          id="search-input"
          placeholder="Cari karyawan..."
          value="{{ $search }}"
          x-data
          x-on:input.debounce.300ms="
            let el = $event.target;
            el.value = el.value.replace(/^\s+/, '');
            
            const val = el.value;
            // Kirim request HANYA jika nilainya berbeda dengan data di server
            if (val !== $wire.search) {
                if (val.trim().length > 0 || val === '') {
                    $wire.set('search', val);
                }
            }
          ">
      </div>

      <div class="table-responsive">
        <table class="table table-vcenter table-bordered mb-2">
          <thead>
            <tr>
              <th class="fs-5 text-center">#</th>
              <th class="fs-5">Nik</th>
              <th class="fs-5">Nama</th>
              <th class="fs-5 text-center">JK</th>
              <th class="fs-5">Penempatan</th>
              <th class="fs-5">Kategori</th>
              <th class="fs-5 text-center">Usia (th)</th>
              <th class="fs-5">Jabatan</th>
              <th class="fs-5 text-center">Status</th>
              @if($hasActions)
              <th class="fs-5 text-center">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $i => $row)
            <tr wire:key="{{ $row->id }}">
              <td class="text-center">{{ $data->firstItem() + $i }}.</td>
              <td class="text-uppercase">{{ $row->nik }}</td>
              <td class="text-uppercase">{{ $row->nama }}</td>
              <td class="text-uppercase text-center">{{ $row->jenis_kelamin }}</td>
              <td class="text-uppercase">{{ $row->lokasi?->nama ?? '-' }}</td>
              <td class="text-uppercase">{{ $row->kategori?->nama ?? '-' }}</td>
              <td class="text-uppercase text-center">{{ $row->usia }}</td>
              <td class="text-uppercase">{{ $row->jabatan?->nama ?? '-' }}</td>
              <td class="text-uppercase text-center">
                @can('karyawan.change_status')
                  <button type="button"
                    class="btn btn-sm rounded-2 border-0
                      {{ $row->status === 'aktif' ? 'btn-success' : '' }}
                      {{ $row->status === 'nonaktif' ? 'btn-danger' : '' }}
                      {{ $row->status === 'vakum' ? 'btn-warning' : '' }}"
                    wire:click="updateStatus({{ $row->id }})"
                    wire:target="updateStatus({{ $row->id }})"
                    wire:loading.attr="disabled"
                    title="Ubah status"
                  >
                    <span 
                      wire:loading 
                      wire:target="updateStatus({{ $row->id }})" 
                      class="spinner-border spinner-border-sm p-2">
                    </span>
                    <span 
                      wire:loading.remove 
                      wire:target="updateStatus({{ $row->id }})"
                      class="text-uppercase">{{ $row->status }}
                    </span>
                  </button>
                @else
                  <span class="badge text-uppercase 
                    {{ $row->status === 'aktif' ? 'badge-outline text-lime' : '' }}
                    {{ $row->status === 'nonaktif' ? 'badge-outline text-red' : '' }}
                    {{ $row->status === 'vakum' ? 'badge-outline text-yellow' : '' }}
                  ">
                    {{ $row->status }}
                  </span>
                @endcan
              </td>
              @if($hasActions)
              <td class="text-center">
                <div class="btn-group" role="group" style="gap: 3px;">
                  @can('karyawan.edit')
                  <button wire:click="edit({{ $row->id }})" wire:loading.attr="disabled" wire:target="edit({{ $row->id }})" title="Edit" class="btn btn-warning btn-sm">
                    <span wire:loading wire:target="edit({{ $row->id }})" class="spinner-border spinner-border-sm p-2"></span>
                    <svg wire:loading.remove wire:target="edit({{ $row->id }})" xmlns="http://www.w3.org/2000/svg"
                      style="width: 18px; height: 18px;"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round" 
                      class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                      <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                      <path d="M16 5l3 3" />
                    </svg>
                  </button>
                  @endcan
                  @can('karyawan.detail')
                  <a x-data="{ isLoading: false }" :href="'{{ route('karyawan.detail', $row->nik) }}?back=' + btoa(window.location.href)" @click="isLoading = true" wire:navigate title="Detail" class="btn btn-info btn-sm">
                   <span x-show="isLoading" class="spinner-border spinner-border-sm" style="display: none;"></span>
                   <svg x-show="!isLoading" xmlns="http://www.w3.org/2000/svg" 
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
                  </a>
                  @endcan
                  @can('karyawan.delete')
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
              <td colspan="{{ $hasActions ? 10 : 9 }}" class="text-center text-muted">Belum ada data.</td>
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

  {{-- ===================== MODAL ===================== --}}
  <livewire:karyawan.edit />
  <livewire:karyawan.status />
  @include('components.modal.confirm')
  @include('livewire.karyawan.filter')
  @include('livewire.karyawan.export')

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:navigated', () => {
    const kategoriFilterSelectEl = document.getElementById('kategori-filter-select')
    const lokasiFilterSelectEl = document.getElementById('lokasi-filter-select')
    const lokasiSelectEl = document.getElementById('lokasi-select')
    const divisiFilterSelectEl = document.getElementById('divisi-filter-select')

    const initTomSelect = (selectEl, hiddenInputId, placeholder) => {
      if (!selectEl) return
      if (selectEl.tomselect) {
        selectEl.tomselect.destroy();
      }

      const hiddenInput = document.getElementById(hiddenInputId)
      
      const ts = new TomSelect(selectEl, {
        allowEmptyOption: true,
        placeholder: placeholder,
        items: [], // Tidak ada item terpilih secara default, hanya tampilkan placeholder
        onChange(value) {
          // Set hidden input value dan trigger input event untuk Livewire
          hiddenInput.value = value
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }))
        }
      })

      // Prevent Enter key from submitting form
      ts.control_input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault()
          e.stopPropagation()
        }
      })

      return ts
    }

    initTomSelect(kategoriFilterSelectEl, 'kategori-filter-hidden', 'Pilih Kategori..')
    initTomSelect(lokasiFilterSelectEl, 'lokasi-filter-hidden', 'Pilih Penempatan..')
    initTomSelect(divisiFilterSelectEl, 'divisi-filter-hidden', 'Pilih Divisi..')

    Livewire.on('openStatus', () => {
      initTomSelect(lokasiSelectEl, 'lokasi-hidden', 'Pilih Penempatan..')
    })
    
    const modalEdit = document.getElementById('editModal');
    const modalConfirm = document.getElementById('confirmModal');
    const modalFilter = document.getElementById('filterModal');
    const modalExport = document.getElementById('exportModal');
    const modalStatus = document.getElementById('statusModal');

    Livewire.on('openEdit', () => toggleModal('editModal', 'show'));
    Livewire.on('closeEdit', () => toggleModal('editModal', 'hide'));

    Livewire.on('openConfirmModal', () => toggleModal('confirmModal', 'show'));
    Livewire.on('closeConfirmModal', () => toggleModal('confirmModal', 'hide'));

    Livewire.on('openFilter', () => toggleModal('filterModal', 'show'));
    Livewire.on('closeFilter', () => toggleModal('filterModal', 'hide'));

    Livewire.on('openExport', () => toggleModal('exportModal', 'show'));
    Livewire.on('closeExport', () => {
      toggleModal('exportModal', 'hide');
      toggleModal('filterModal', 'hide');
    });

    Livewire.on('openStatus', () =>  toggleModal('statusModal', 'show'));
    Livewire.on('closeStatus', () => toggleModal('statusModal', 'hide'));

    const clearHidden = (id) => {
      const el = document.getElementById(id)
      if (el) el.value = ''
    }

    Livewire.on('reset-tomselect', () => {
      kategoriFilterSelectEl?.tomselect?.clear()
      lokasiFilterSelectEl?.tomselect?.clear()
      divisiFilterSelectEl?.tomselect?.clear()
       
      clearHidden('kategori-filter-hidden')
      clearHidden('lokasi-filter-hidden')
      clearHidden('divisi-filter-hidden')
    });

    const dateFields = ['tgl-masuk-start', 'tgl-masuk-end', 'tglMulai', 'tglSelesai', 'tglKeluar', 'tglEfektif', 'tglLahir'];

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

    Livewire.on('open-pdf', () => window.open('/karyawan/export-pdf', '_blank'));

    blurActiveElementOnModalHide([modalEdit, modalConfirm, modalFilter, modalExport, modalStatus]);
  })

</script>
@endpush