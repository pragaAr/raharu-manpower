<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Absensi' : 'Tambah Absensi' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label class="form-label">Karyawan</label>
            <input type="hidden" id="karyawan-hidden" wire:model.defer="karyawanId">
            <div wire:ignore>
              <select id="karyawan-select" class="form-select">
                @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                @endforeach
              </select>
            </div>
            <div id="karyawan-error">
              @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label">Tanggal</label>
            <input type="date" wire:model.defer="tanggal" class="form-control text-uppercase @error('tanggal') is-invalid @enderror" autocomplete="off"id="tanggal" placeholder="Tanggal">
            @error('tanggal')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div 
            x-data="{
                masuk: @entangle('masuk').live,
                pulang: @entangle('pulang').live
            }">

            <div class="mb-2">
              <label class="form-label">Masuk</label>
              <input type="time" 
                wire:model.defer="masuk" 
                x-model="masuk"
                class="form-control text-uppercase @error('masuk') is-invalid @enderror" 
                id="masuk" autocomplete="off" placeholder="Jam masuk">
              @error('masuk')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label class="form-label">Keterangan Masuk</label>
              <input type="text"
                wire:model.defer="keteranganMasuk"
                class="form-control @error('keteranganMasuk') is-invalid @enderror"
                :disabled="!masuk"
                autocomplete="off"
                placeholder="Alasan input jam masuk">

              @error('keteranganMasuk')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label class="form-label">Pulang</label>
              <input type="time" 
                wire:model.defer="pulang" 
                x-model="pulang"
                class="form-control text-uppercase @error('pulang') is-invalid @enderror" 
                id="pulang" autocomplete="off" placeholder="Jam pulang">
              @error('pulang')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label class="form-label">Keterangan Pulang</label>
              <input type="text"
                wire:model.defer="keteranganPulang"
                class="form-control @error('keteranganPulang') is-invalid @enderror"
                :disabled="!pulang"
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