<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Jabatan' : 'Tambah Jabatan' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-3">
            <label class="form-label">Unit</label>
            <input type="hidden" id="unit-hidden" wire:model="unit_id">
            <div wire:ignore>
              <select id="unit-select" class="form-select text-uppercase">
                @foreach ($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->divisi_nama }} - {{ $unit->nama }}</option>
                @endforeach
              </select>
            </div>

            <div id="unit-error">
              @error('unit_id')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" wire:model.defer="nama"
              class="form-control text-uppercase @error('nama') is-invalid @enderror" autocomplete="off" placeholder="nama jabatan">
            @error('nama')
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