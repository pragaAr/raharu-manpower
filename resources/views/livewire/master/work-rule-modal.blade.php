<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Aturan Kerja' : 'Tambah Aturan Kerja' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="work-rule-form" wire:submit.prevent="save">
          <div class="mb-3">
            <label for="jabatan-select" class="form-label">Jabatan</label>
            <input type="hidden" id="jabatan-hidden" wire:model="jabatan_id">
            <div wire:ignore>
              <select id="jabatan-select" class="form-select text-uppercase">
                @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}">{{ $jabatan->divisi_nama }} - {{ $jabatan->unit_nama }} - {{ $jabatan->nama }}</option>
                @endforeach
              </select>
            </div>
            <div id="jabatan-error">
              @error('jabatan_id')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="use-shift" wire:model.live="use_shift">
                <label class="form-check-label" for="use-shift">Gunakan Shift</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="auto-overtime" wire:model="auto_overtime">
                <label class="form-check-label" for="auto-overtime">Lembur Otomatis</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="overtime-need-approval" wire:model="overtime_need_approval">
                <label class="form-check-label" for="overtime-need-approval">Lembur Butuh Approval</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="cuti-need-approval" wire:model="cuti_need_approval">
                <label class="form-check-label" for="cuti-need-approval">Cuti Butuh Approval</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="allow-double-shift" wire:model="allow_double_shift">
                <label class="form-check-label" for="allow-double-shift">Izinkan Double Shift</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="allow-shift-swap" wire:model="allow_shift_swap">
                <label class="form-check-label" for="allow-shift-swap">Izinkan Tukar Shift</label>
              </div>
            </div>
          </div>

          <div class="alert alert-info py-2 px-3 mt-2 mb-3">
            Jika <strong>Gunakan Shift</strong> aktif, jam kerja per hari akan disimpan kosong dan mengikuti master shift.
          </div>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th style="width: 25%;">Hari</th>
                  <th class="text-center" style="width: 20%;">Hari Kerja</th>
                  <th style="width: 27.5%;">Jam Masuk</th>
                  <th style="width: 27.5%;">Jam Pulang</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dayLabels as $dayNumber => $dayLabel)
                <tr wire:key="work-day-{{ $dayNumber }}">
                  <td>{{ $dayLabel }}</td>
                  <td class="text-center">
                    <input class="form-check-input" type="checkbox" wire:model.live="days.{{ $dayNumber }}.is_workday">
                  </td>
                  <td>
                    <input type="time"
                      wire:model.defer="days.{{ $dayNumber }}.jam_masuk"
                      class="form-control @error('days.' . $dayNumber . '.jam_masuk') is-invalid @enderror"
                      @disabled($use_shift || !data_get($days, $dayNumber . '.is_workday'))>
                    @error('days.' . $dayNumber . '.jam_masuk')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </td>
                  <td>
                    <input type="time"
                      wire:model.defer="days.{{ $dayNumber }}.jam_pulang"
                      class="form-control @error('days.' . $dayNumber . '.jam_pulang') is-invalid @enderror"
                      @disabled($use_shift || !data_get($days, $dayNumber . '.is_workday'))>
                    @error('days.' . $dayNumber . '.jam_pulang')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
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
