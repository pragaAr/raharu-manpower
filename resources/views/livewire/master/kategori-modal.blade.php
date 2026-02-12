<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label class="form-label">Kategori</label>
            <input type="text" wire:model.defer="nama"
              class="form-control text-uppercase @error('nama') is-invalid @enderror" autofocus autocomplete="off" placeholder="nama kategori">
            @error('nama')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-2">
            <label class="form-label">Keterangan</label>
            <input type="text" wire:model.defer="keterangan" class="form-control text-uppercase @error('keterangan') is-invalid @enderror" autocomplete="off" placeholder="keterangan kategori">
            @error('keterangan')
            <small class="text-danger">{{ $message }}</small>
            @enderror
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