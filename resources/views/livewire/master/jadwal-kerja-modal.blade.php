<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Jadwal Kerja' : 'Tambah Jadwal Kerja' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="jadwal-kerja-form" wire:submit.prevent="save">
          <div class="mb-2">
            <label for="jadwal-kerja-karyawanSelect" class="form-label">Karyawan</label>
            <input type="hidden" id="jadwal-kerja-karyawanHidden" wire:model.defer="karyawanId">
            <div wire:ignore>
              <select id="jadwal-kerja-karyawanSelect" class="form-select">
                @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                @endforeach
              </select>
            </div>
            <div id="jadwal-kerja-karyawanError">
              @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="mb-2">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" id="tanggal" wire:model.live="tanggal"
              class="form-control @error('tanggal') is-invalid @enderror">
            @error('tanggal')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div x-data="{ isLibur: @entangle('isLibur').live, shiftId: @entangle('shiftId').live }">
            <div class="mb-2">
              <label for="jadwal-kerja-shiftSelect" class="form-label">Shift (Opsional)</label>
              <input type="hidden" id="jadwal-kerja-shiftHidden" wire:model.live="shiftId">
              <div wire:ignore>
                <select id="jadwal-kerja-shiftSelect" class="form-select">
                  @foreach ($shifts as $shift)
                  <option value="{{ $shift->id }}">
                    {{ strtoupper($shift->nama) }} ({{ $shift->jam_masuk?->format('H:i') ?? '--:--' }} - {{ $shift->jam_pulang?->format('H:i') ?? '--:--' }})
                  </option>
                  @endforeach
                </select>
              </div>
              <div id="jadwal-kerja-shiftError">
                @error('shiftId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="mb-2">
                  <label for="jam-masuk" class="form-label">Jam Masuk</label>
                  <input type="time" id="jam-masuk" wire:model.defer="jamMasuk"
                    :disabled="isLibur || !!shiftId"
                    class="form-control @error('jamMasuk') is-invalid @enderror">
                  @error('jamMasuk')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <div class="col-6">
                <div class="mb-2">
                  <label for="jam-pulang" class="form-label">Jam Pulang</label>
                  <input type="time" id="jam-pulang" wire:model.defer="jamPulang"
                    :disabled="isLibur || !!shiftId"
                    class="form-control @error('jamPulang') is-invalid @enderror">
                  @error('jamPulang')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row mt-1">
              <div class="col-6">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="is-libur" wire:model.live="isLibur">
                  <label class="form-check-label" for="is-libur">Libur</label>
                </div>
              </div>

              <div class="col-6">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="is-holiday" wire:model="isHoliday">
                  <label class="form-check-label" for="is-holiday">Holiday</label>
                </div>
              </div>
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
