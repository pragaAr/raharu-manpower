<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">{{ $isEdit ? 'Edit Pengajuan Double Shift' : 'Tambah Pengajuan Double Shift' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label for="double-shift-karyawan-id" class="form-label">Karyawan</label>
            <select id="double-shift-karyawan-id" wire:model.defer="karyawanId"
              class="form-select @error('karyawanId') is-invalid @enderror">
              <option value="">Pilih karyawan</option>
              @foreach ($karyawans as $karyawan)
              <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
              @endforeach
            </select>
            @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="double-shift-tanggal" class="form-label">Tanggal</label>
            <input type="date" id="double-shift-tanggal" wire:model.defer="tanggal"
              class="form-control @error('tanggal') is-invalid @enderror">
            @error('tanggal') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="double-shift-shift-awal-id" class="form-label">Shift Awal</label>
                <select id="double-shift-shift-awal-id" wire:model.defer="shiftAwalId"
                  class="form-select @error('shiftAwalId') is-invalid @enderror">
                  <option value="">Pilih shift awal</option>
                  @foreach ($shifts as $shift)
                  <option value="{{ $shift->id }}">
                    {{ strtoupper($shift->nama) }} ({{ $shift->jam_masuk?->format('H:i') ?? '-' }} - {{ $shift->jam_pulang?->format('H:i') ?? '-' }})
                  </option>
                  @endforeach
                </select>
                @error('shiftAwalId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <label for="double-shift-shift-tambahan-id" class="form-label">Shift Tambahan</label>
                <select id="double-shift-shift-tambahan-id" wire:model.defer="shiftTambahanId"
                  class="form-select @error('shiftTambahanId') is-invalid @enderror">
                  <option value="">Pilih shift tambahan</option>
                  @foreach ($shifts as $shift)
                  <option value="{{ $shift->id }}">
                    {{ strtoupper($shift->nama) }} ({{ $shift->jam_masuk?->format('H:i') ?? '-' }} - {{ $shift->jam_pulang?->format('H:i') ?? '-' }})
                  </option>
                  @endforeach
                </select>
                @error('shiftTambahanId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="double-shift-catatan" class="form-label">Catatan</label>
            <textarea id="double-shift-catatan" wire:model.defer="catatan" rows="2"
              class="form-control @error('catatan') is-invalid @enderror" placeholder="catatan double shift"></textarea>
            @error('catatan') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="double-shift-status" class="form-label">Status</label>
            <select id="double-shift-status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
              @foreach ($statusOptions as $option)
              <option value="{{ $option }}">{{ strtoupper($option) }}</option>
              @endforeach
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="double-shift-approver-id" class="form-label">Approver</label>
            <select id="double-shift-approver-id" wire:model.defer="approvedBy"
              class="form-select @error('approvedBy') is-invalid @enderror"
              @disabled($status !== 'approved')>
              <option value="">Pilih approver</option>
              @foreach ($approvers as $approver)
              <option value="{{ $approver->id }}">
                {{ strtoupper($approver->username) }} - {{ strtoupper($approver->karyawan->nama ?? '-') }}
              </option>
              @endforeach
            </select>
            @error('approvedBy') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="double-shift-approved-at" class="form-label">Waktu Approval</label>
            <input type="datetime-local" id="double-shift-approved-at" wire:model.defer="approvedAt"
              class="form-control @error('approvedAt') is-invalid @enderror"
              @disabled($status !== 'approved')>
            @error('approvedAt') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
              {{ $isEdit ? 'Update' : 'Simpan' }}
              <span wire:loading wire:target="save" class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
