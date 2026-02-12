<div wire:ignore.self class="modal fade" id="statusModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Ubah Status Karyawan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body" 
        x-data="{
          selectedStatus: @entangle('status'),
          statusAkhir: @entangle('statusAkhir')
        }"
        >
        <form wire:submit.prevent="updateStatus" class="row g-2">

          <div class="col-md-12">
            <div class="form-label">Status</div>

            <label class="form-check form-check-inline">
              <input type="radio" class="form-check-input"
                x-model="selectedStatus" wire:model.live="status" value="aktif">
              Aktif
            </label>

            <label class="form-check form-check-inline">
              <input type="radio" class="form-check-input"
                x-model="selectedStatus" wire:model.live="status" value="nonaktif">
              Non Aktif
            </label>

            <label class="form-check form-check-inline">
              <input type="radio" class="form-check-input"
                x-model="selectedStatus" wire:model.live="status" value="vakum">
              Vakum
            </label>

            @error('status')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          {{-- Input Dari & Sampai: hanya muncul jika status = vakum --}}
          <div class="col-md-12" x-show="selectedStatus === 'vakum'" x-cloak>
            <label for="tglMulai" class="form-label">Dari</label>
            <input type="date" class="form-control"
              wire:model="tglMulai" id="tglMulai">
            @error('tglMulai') 
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="col-md-12" x-show="selectedStatus === 'vakum'" x-cloak>
            <label for="tglSelesai" class="form-label">Sampai</label>
            <input type="date" class="form-control"
              wire:model="tglSelesai" id="tglSelesai">
            @error('tglSelesai') 
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          {{-- Input Tanggal Keluar: hanya muncul jika status = nonaktif --}}
          <div class="col-md-12" x-show="selectedStatus === 'nonaktif'" x-cloak>
            <label for="tglKeluar" class="form-label">Tanggal Keluar</label>
            <input type="date" class="form-control"
              wire:model="tglKeluar" id="tglKeluar">
            @error('tglKeluar') 
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="col-md-12"
            x-show="statusAkhir === 'nonaktif' && selectedStatus === 'aktif'"
            x-cloak>
            <small class="text-muted d-block">
              Karyawan akan aktif kembali dengan status probation.
            </small>

            <label for="lokasi-select" class="form-label">Penempatan</label>

            {{-- bridge ke Livewire --}}
            <input type="hidden" id="lokasi-hidden" wire:model="lokasiId">

            <div wire:ignore>
              <select id="lokasi-select" class="form-select">
                @foreach ($lokasis as $lokasi)
                  <option value="{{ $lokasi->id }}">
                    {{ strtoupper($lokasi->nama) }}
                  </option>
                @endforeach
              </select>
            </div>

            @error('lokasiId')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="col-md-12" 
            x-show="statusAkhir === 'nonaktif' && selectedStatus === 'aktif'"
            x-cloak>
            <label for="tglEfektif" class="form-label">Efektif</label>
            <input type="date" id="tglEfektif" class="form-control @error('tglEfektif') is-invalid @enderror" wire:model="tglEfektif" id="tglEfektif" autocomplete="off">
            @error('tglEfektif')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="col-md-12 mb-3"
            x-show="statusAkhir !== 'aktif' || selectedStatus !== 'aktif'"
            x-cloak
          >
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text"
              class="form-control"
              wire:model.defer="keterangan"
              id="keterangan"
              placeholder="Keterangan perubahan status">
            @error('keterangan')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="col-md-12">
            <button type="submit"
              wire:loading.attr="disabled"
              wire:target="updateStatus"
              :class="{ 'disabled': selectedStatus === statusAkhir }"
              :disabled="selectedStatus === statusAkhir"
              class="btn btn-primary w-100">
              Ubah Status
              <span wire:loading wire:target="updateStatus" class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>