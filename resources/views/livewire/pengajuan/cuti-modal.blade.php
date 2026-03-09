<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">{{ $isEdit ? 'Edit Pengajuan Cuti' : 'Tambah Pengajuan Cuti' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label for="cuti-karyawan-id" class="form-label">Karyawan</label>
            <select id="cuti-karyawan-id" wire:model.defer="karyawanId" class="form-select @error('karyawanId') is-invalid @enderror">
              <option value="">Pilih karyawan</option>
              @foreach ($karyawans as $karyawan)
              <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
              @endforeach
            </select>
            @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="cuti-tanggal-mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" id="cuti-tanggal-mulai" wire:model.defer="tanggalMulai"
                  class="form-control @error('tanggalMulai') is-invalid @enderror">
                @error('tanggalMulai') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <label for="cuti-tanggal-selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" id="cuti-tanggal-selesai" wire:model.defer="tanggalSelesai"
                  class="form-control @error('tanggalSelesai') is-invalid @enderror">
                @error('tanggalSelesai') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="cuti-alasan" class="form-label">Alasan</label>
            <textarea id="cuti-alasan" wire:model.defer="alasan" rows="2"
              class="form-control @error('alasan') is-invalid @enderror" placeholder="alasan cuti"></textarea>
            @error('alasan') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="cuti-status" class="form-label">Status</label>
            <select id="cuti-status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
              @foreach ($statusOptions as $option)
              <option value="{{ $option }}">{{ strtoupper($option) }}</option>
              @endforeach
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="cuti-approver-id" class="form-label">Approver</label>
            <select id="cuti-approver-id" wire:model.defer="approvedBy" class="form-select @error('approvedBy') is-invalid @enderror"
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
            <label for="cuti-approved-at" class="form-label">Waktu Approval</label>
            <input type="datetime-local" id="cuti-approved-at" wire:model.defer="approvedAt"
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
