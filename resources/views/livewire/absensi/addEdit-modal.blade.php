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
            <input type="hidden" id="karyawanId-hidden" wire:model.defer="karyawanId">
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
            <input type="date" wire:model.defer="tanggal" class="form-control text-uppercase @error('tanggal') is-invalid @enderror" autocomplete="off" placeholder="Tanggal">
            @error('tanggal')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-2">
            <label class="form-label">Masuk</label>
            <input type="time" wire:model.defer="masuk" class="form-control text-uppercase @error('masuk') is-invalid @enderror" id="masuk" autocomplete="off" placeholder="Jam masuk">
            @error('masuk')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

           <div class="mb-2">
            <label class="form-label">Pulang</label>
            <input type="time" wire:model.defer="pulang" class="form-control text-uppercase @error('pulang') is-invalid @enderror" id="pulang" autocomplete="off" placeholder="Jam pulang">
            @error('pulang')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-2">
            <label class="form-label">Keterangan</label>
            <input type="text" wire:model.defer="keterangan" class="form-control text-uppercase @error('keterangan') is-invalid @enderror" autocomplete="off" placeholder="Keterangan">
            @error('keterangan')
            <small class="text-danger">{{ $message }}</small>
            @enderror
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