<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">{{ $isEdit ? 'Edit Pengajuan Perubahan Lembur' : 'Tambah Pengajuan Perubahan Lembur' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label for="perubahan-lembur-karyawan-id" class="form-label">Karyawan</label>
            <select id="perubahan-lembur-karyawan-id" wire:model.live="karyawanId"
              class="form-select @error('karyawanId') is-invalid @enderror">
              <option value="">Pilih karyawan</option>
              @foreach ($karyawans as $karyawan)
              <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
              @endforeach
            </select>
            @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="perubahan-lembur-jadwal-id" class="form-label">Jadwal Lembur</label>
            <select id="perubahan-lembur-jadwal-id" wire:model.live="jadwalLemburId"
              class="form-select @error('jadwalLemburId') is-invalid @enderror">
              <option value="">Pilih jadwal lembur</option>
              @foreach ($jadwalLemburs as $jadwal)
              <option value="{{ $jadwal->id }}">
                {{ $jadwal->tanggal?->format('d-m-Y') }} ({{ $jadwal->jam_mulai?->format('H:i') ?? '-' }} - {{ $jadwal->jam_selesai?->format('H:i') ?? '-' }})
              </option>
              @endforeach
            </select>
            @error('jadwalLemburId') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="row">
            <div class="col-4">
              <div class="mb-2">
                <label for="perubahan-lembur-tanggal" class="form-label">Tanggal</label>
                <input type="date" id="perubahan-lembur-tanggal" wire:model.defer="tanggal" class="form-control" disabled>
              </div>
            </div>
            <div class="col-4">
              <div class="mb-2">
                <label for="perubahan-lembur-jam-mulai-lama" class="form-label">Mulai Lama</label>
                <input type="time" id="perubahan-lembur-jam-mulai-lama" wire:model.defer="jamMulaiLama" class="form-control" disabled>
              </div>
            </div>
            <div class="col-4">
              <div class="mb-2">
                <label for="perubahan-lembur-jam-selesai-lama" class="form-label">Selesai Lama</label>
                <input type="time" id="perubahan-lembur-jam-selesai-lama" wire:model.defer="jamSelesaiLama" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="perubahan-lembur-jam-mulai-baru" class="form-label">Jam Mulai Baru</label>
                <input type="time" id="perubahan-lembur-jam-mulai-baru" wire:model.defer="jamMulaiBaru"
                  class="form-control @error('jamMulaiBaru') is-invalid @enderror">
                @error('jamMulaiBaru') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <label for="perubahan-lembur-jam-selesai-baru" class="form-label">Jam Selesai Baru</label>
                <input type="time" id="perubahan-lembur-jam-selesai-baru" wire:model.defer="jamSelesaiBaru"
                  class="form-control @error('jamSelesaiBaru') is-invalid @enderror">
                @error('jamSelesaiBaru') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="perubahan-lembur-alasan" class="form-label">Alasan</label>
            <textarea id="perubahan-lembur-alasan" wire:model.defer="alasan" rows="2"
              class="form-control @error('alasan') is-invalid @enderror" placeholder="alasan perubahan lembur"></textarea>
            @error('alasan') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="perubahan-lembur-status" class="form-label">Status</label>
            <select id="perubahan-lembur-status" wire:model.live="status" class="form-select @error('status') is-invalid @enderror">
              @foreach ($statusOptions as $option)
              <option value="{{ $option }}">{{ strtoupper($option) }}</option>
              @endforeach
            </select>
            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="mb-2">
            <label for="perubahan-lembur-approver-id" class="form-label">Approver</label>
            <select id="perubahan-lembur-approver-id" wire:model.defer="approvedBy"
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
            <label for="perubahan-lembur-approved-at" class="form-label">Waktu Approval</label>
            <input type="datetime-local" id="perubahan-lembur-approved-at" wire:model.defer="approvedAt"
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
