<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title">{{ $isEdit ? 'Edit Role' : 'Tambah Role' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form wire:submit.prevent="save">
          <div class="mb-3">
            <label class="form-label">Nama Role</label>
            <input type="text" wire:model.live.debounce.300ms="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Manager" autofocus autocomplete="off">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Izin Akses</label>
            <div class="row">
              @foreach($groupedPermissions as $module => $items)
              <div class="col-12 mb-3">
                <div class="hr-text fs-5 mt-2 mb-3">{{ $module }}</div>
                <div class="row">
                  @foreach($items as $p)
                  <div class="col-md-3 col-sm-3 mb-2">
                    <label class="form-check">
                      <input class="form-check-input" type="checkbox" wire:model.live="selectedPermissions" value="{{ (string)$p->id }}">
                      <span class="form-check-label">{{ ucfirst($p->label ? explode(' ', $p->label)[0] : (explode('.', $p->name)[1] ?? $p->name)) }}</span>
                    </label>
                  </div>
                  @endforeach
                </div>
              </div>
              @endforeach
            </div>
          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
              <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></span>
              {{ $isEdit ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>