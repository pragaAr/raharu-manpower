<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Hari Libur' : 'Tambah Hari Libur' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" id="tanggal" wire:model.defer="tanggal"
              class="form-control @error('tanggal') is-invalid @enderror">
            @error('tanggal')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-2">
            <label for="nama" class="form-label">Nama Hari Libur</label>
            <input type="text" id="nama" wire:model.defer="nama"
              class="form-control text-uppercase @error('nama') is-invalid @enderror" autocomplete="off" placeholder="nama hari libur">
            @error('nama')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is-national" wire:model="is_national">
            <label class="form-check-label" for="is-national">Hari libur nasional</label>
          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
              wire:target="save">
              {{ $isEdit ? 'Update' : 'Simpan' }}
              <span wire:loading class="spinner-border spinner-border-sm ms-2"></span>
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
