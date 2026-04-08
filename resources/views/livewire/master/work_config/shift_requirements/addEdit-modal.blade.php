<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Shift Requirement' : 'Tambah Shift Requirement' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="shift-requirement-form" wire:submit.prevent="save">
          <div class="mb-3">
            <label for="lokasi-select" class="form-label">Lokasi</label>
            <input type="hidden" id="lokasi-hidden" wire:model="lokasi_id">
            <div wire:ignore>
              <select id="lokasi-select" class="form-select text-uppercase">
                @foreach ($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                @endforeach
              </select>
            </div>
            <div id="lokasi-error">
              @error('lokasi_id')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="shift-select" class="form-label">Shift</label>
            <input type="hidden" id="shift-hidden" wire:model="shift_id">
            <div wire:ignore>
              <select id="shift-select" class="form-select text-uppercase">
                @foreach ($shifts as $shift)
                <option value="{{ $shift->id }}">{{ $shift->nama }}</option>
                @endforeach
              </select>
            </div>
            <div id="shift-error">
              @error('shift_id')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="required-count-input" class="form-label">Kebutuhan</label>
            <input type="number" id="required-count-input" wire:model.defer="required_count"
              class="form-control @error('required_count') is-invalid @enderror"
              min="0" step="1" placeholder="jumlah kebutuhan">
            @error('required_count')
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
