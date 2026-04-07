<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Jadwal Lembur' : 'Tambah Jadwal Lembur' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="jadwal-lembur-form" wire:submit.prevent="save">
          <div class="mb-2">
            <label for="jadwal-lembur-karyawanSelect" class="form-label">Karyawan</label>
            <input type="hidden" id="jadwal-lembur-karyawanHidden" wire:model.defer="karyawanId">
            <div wire:ignore>
              <select id="jadwal-lembur-karyawanSelect" class="form-select">
                @foreach ($karyawans as $karyawan)
                <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                @endforeach
              </select>
            </div>
            <div id="jadwal-lembur-karyawanError">
              @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" id="tanggal" wire:model.defer="tanggal"
                  class="form-control @error('tanggal') is-invalid @enderror">
                @error('tanggal')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <div class="col-6">
              <div class="mb-2">
                <label for="type" class="form-label">Tipe</label>
                <select id="type" wire:model.defer="type" class="form-select @error('type') is-invalid @enderror">
                  @foreach ($typeOptions as $typeOption)
                  <option value="{{ $typeOption }}">{{ strtoupper($typeOption) }}</option>
                  @endforeach
                </select>
                @error('type')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="jam-mulai" class="form-label">Jam Mulai</label>
                <input type="time" id="jam-mulai" wire:model.defer="jamMulai"
                  class="form-control @error('jamMulai') is-invalid @enderror">
                @error('jamMulai')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <div class="col-6">
              <div class="mb-2">
                <label for="jam-selesai" class="form-label">Jam Selesai</label>
                <input type="time" id="jam-selesai" wire:model.defer="jamSelesai"
                  class="form-control @error('jamSelesai') is-invalid @enderror">
                @error('jamSelesai')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div x-data="{ status: @entangle('status').live }">
            <div class="mb-2">
              <label for="status" class="form-label">Status</label>
              <select id="status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
                @foreach ($statusOptions as $statusOption)
                <option value="{{ $statusOption }}">{{ strtoupper($statusOption) }}</option>
                @endforeach
              </select>
              @error('status')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="mb-2">
              <label for="jadwal-lembur-approverSelect" class="form-label">Approver</label>
              <input type="hidden" id="jadwal-lembur-approverHidden" wire:model.defer="approvedBy">
              <div wire:ignore>
                <select id="jadwal-lembur-approverSelect" class="form-select" :disabled="status !== 'approved'">
                  @foreach ($approvers as $approver)
                  <option value="{{ $approver->id }}">
                    {{ strtoupper($approver->username) }} - {{ strtoupper($approver->karyawan->nama ?? '-') }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div id="jadwal-lembur-approverError">
                @error('approvedBy') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>

            <div class="mb-2">
              <label for="approved-at" class="form-label">Waktu Approval</label>
              <input type="datetime-local" id="approved-at" wire:model.defer="approvedAt"
                :disabled="status !== 'approved'"
                class="form-control @error('approvedAt') is-invalid @enderror">
              @error('approvedAt')
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
