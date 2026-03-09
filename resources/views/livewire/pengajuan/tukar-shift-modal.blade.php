<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">{{ $isEdit ? 'Edit Pengajuan Tukar Shift' : 'Tambah Pengajuan Tukar Shift' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="tukar-shift-requester-id" class="form-label">Requester</label>
                <select id="tukar-shift-requester-id" wire:model.live="requesterId" class="form-select @error('requesterId') is-invalid @enderror">
                  <option value="">Pilih requester</option>
                  @foreach ($karyawans as $karyawan)
                  <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                  @endforeach
                </select>
                @error('requesterId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <label for="tukar-shift-target-id" class="form-label">Target Karyawan</label>
                <select id="tukar-shift-target-id" wire:model.live="targetKaryawanId" class="form-select @error('targetKaryawanId') is-invalid @enderror">
                  <option value="">Pilih target</option>
                  @foreach ($karyawans as $karyawan)
                  <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                  @endforeach
                </select>
                @error('targetKaryawanId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="tukar-shift-tanggal" class="form-label">Tanggal</label>
            <input type="date" id="tukar-shift-tanggal" wire:model.live="tanggal"
              class="form-control @error('tanggal') is-invalid @enderror">
            @error('tanggal') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="tukar-shift-requester-jadwal-id" class="form-label">Jadwal Requester</label>
                <select id="tukar-shift-requester-jadwal-id" wire:model.defer="requesterJadwalId"
                  class="form-select @error('requesterJadwalId') is-invalid @enderror">
                  <option value="">Pilih jadwal requester</option>
                  @foreach ($requesterJadwals as $jadwal)
                  <option value="{{ $jadwal->id }}">
                    {{ strtoupper($jadwal->shift->nama ?? $jadwal->shift_nama ?? '-') }} ({{ $jadwal->jam_masuk?->format('H:i') ?? '-' }} - {{ $jadwal->jam_pulang?->format('H:i') ?? '-' }})
                  </option>
                  @endforeach
                </select>
                @error('requesterJadwalId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <label for="tukar-shift-target-jadwal-id" class="form-label">Jadwal Target</label>
                <select id="tukar-shift-target-jadwal-id" wire:model.defer="targetJadwalId"
                  class="form-select @error('targetJadwalId') is-invalid @enderror">
                  <option value="">Pilih jadwal target</option>
                  @foreach ($targetJadwals as $jadwal)
                  <option value="{{ $jadwal->id }}">
                    {{ strtoupper($jadwal->shift->nama ?? $jadwal->shift_nama ?? '-') }} ({{ $jadwal->jam_masuk?->format('H:i') ?? '-' }} - {{ $jadwal->jam_pulang?->format('H:i') ?? '-' }})
                  </option>
                  @endforeach
                </select>
                @error('targetJadwalId') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="tukar-shift-catatan" class="form-label">Catatan</label>
            <textarea id="tukar-shift-catatan" wire:model.defer="catatan" rows="2"
              class="form-control @error('catatan') is-invalid @enderror" placeholder="catatan tukar shift"></textarea>
            @error('catatan') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="tukar-shift-status" class="form-label">Status</label>
            <select id="tukar-shift-status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
              @foreach ($statusOptions as $option)
              <option value="{{ $option }}">{{ strtoupper($option) }}</option>
              @endforeach
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="tukar-shift-approver-id" class="form-label">Approver</label>
            <select id="tukar-shift-approver-id" wire:model.defer="approvedBy"
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
            <label for="tukar-shift-approved-at" class="form-label">Waktu Approval</label>
            <input type="datetime-local" id="tukar-shift-approved-at" wire:model.defer="approvedAt"
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
