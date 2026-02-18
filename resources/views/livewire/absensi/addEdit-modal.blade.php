<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Absensi' : 'Tambah Absensi' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="absensi-form" wire:submit.prevent="save">
          <div class="mb-2">
            <label for="absensi-karyawanSelect" class="form-label">Karyawan</label>
            <input type="hidden" id="absensi-karyawanHidden" wire:model.defer="karyawanId">
            <div wire:ignore>
              <select id="absensi-karyawanSelect" class="form-select">
                @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                @endforeach
              </select>
            </div>
            <div id="absensi-karyawanError">
              @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="mb-2">
            <div class="form-label">Status</div>

            <div class="d-flex justify-content-evenly">
              @foreach($statusOptions as $option)
                <div class="form-check form-check-inline" style="margin-right:2px;">
                  <input 
                      class="form-check-input"
                      type="radio"
                      id="status_{{ $option }}"
                      wire:model="status"
                      value="{{ $option }}">
                  <label class="form-check-label text-capitalize"for="status_{{ $option }}" style="margin-left: -6px;">
                    {{ $option }}
                  </label>
                </div>
              @endforeach
            </div>

            @error('status')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-2">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" wire:model.defer="tanggal" class="form-control text-uppercase @error('tanggal') is-invalid @enderror" autocomplete="off" id="tanggal" placeholder="Tanggal">
            @error('tanggal')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div 
            x-data="{
              status: @entangle('status').live,
              masuk: @entangle('masuk').live,
              pulang: @entangle('pulang').live,

              init() {
                this.$watch('status', value => {
                  if (value !== 'hadir') {
                    this.masuk = null
                    this.pulang = null
                  }
                })
              }
            }">

            <div class="mb-2">
              <label for="masuk" class="form-label">Masuk</label>

              <div class="position-relative">
                <input type="time" 
                  wire:model.defer="masuk" 
                  x-model="masuk"
                  :disabled="status !== 'hadir'"
                  class="form-control @error('masuk') is-invalid @enderror" 
                  id="masuk" 
                  autocomplete="off">

                <button type="button" 
                  x-show="masuk" 
                  x-on:click="masuk = null" 
                  class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-5"
                  style="z-index: 10; border: none; box-shadow: none; background: transparent;">
                    Clear
                </button>
              </div>
              @error('masuk')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label for="keteranganMasuk" class="form-label">Keterangan Masuk</label>
              <input type="text"
                wire:model.defer="keteranganMasuk"
                class="form-control @error('keteranganMasuk') is-invalid @enderror"
                id="keteranganMasuk"
                :disabled="status !== 'hadir'"
                autocomplete="off"
                placeholder="Alasan input jam masuk">

              @error('keteranganMasuk')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label for="pulang" class="form-label">Pulang</label>

              <div class="position-relative">
                <input type="time" 
                  wire:model.defer="pulang" 
                  x-model="pulang"
                  :disabled="status !== 'hadir'"
                  class="form-control @error('pulang') is-invalid @enderror" 
                  id="pulang" 
                  autocomplete="off">

                <button type="button" 
                  x-show="pulang" 
                  x-on:click="pulang = null" 
                  class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-5"
                  style="z-index: 10; border: none; box-shadow: none; background: transparent;">
                    Clear
                </button>
              </div>
              @error('pulang')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label for="keteranganPulang" class="form-label">Keterangan Pulang</label>
              <input type="text"
                wire:model.defer="keteranganPulang"
                class="form-control @error('keteranganPulang') is-invalid @enderror"
                :disabled="status !== 'hadir' || !pulang"
                id="keteranganPulang"
                autocomplete="off"
                placeholder="Alasan input jam pulang">

              @error('keteranganPulang')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
              wire:target="save">
              {{ $isEdit ? 'Update' : 'Simpan' }}
              <span wire:loading wire:target="save" class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>