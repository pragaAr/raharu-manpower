<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Shift' : 'Tambah Shift' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-2">
            <label for="nama" class="form-label">Nama Shift</label>
            <input type="text" id="nama" wire:model.defer="nama"
              class="form-control text-uppercase @error('nama') is-invalid @enderror" autofocus autocomplete="off" placeholder="contoh: pagi">
            @error('nama')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="jam-masuk" class="form-label">Jam Masuk</label>
                <input type="time" id="jam-masuk" wire:model.defer="jam_masuk"
                  class="form-control @error('jam_masuk') is-invalid @enderror">
                @error('jam_masuk')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <div class="col-6">
              <div class="mb-2">
                <label for="jam-pulang" class="form-label">Jam Pulang</label>
                <input type="time" id="jam-pulang" wire:model.defer="jam_pulang"
                  class="form-control @error('jam_pulang') is-invalid @enderror">
                @error('jam_pulang')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is-active" wire:model="is_active">
            <label class="form-check-label" for="is-active">Shift aktif</label>
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
