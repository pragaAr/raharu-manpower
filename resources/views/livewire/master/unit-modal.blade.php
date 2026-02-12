<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $isEdit ? 'Edit Unit' : 'Tambah Unit' }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="save">
            
          <div class="mb-3">
            <label class="form-label">Divisi</label>
            <input type="hidden" id="divisi-hidden" wire:model="divisi_id">
            <div wire:ignore>
              <select id="divisi-select" class="form-select text-uppercase">
                @foreach ($divisis as $divisi)
                <option value="{{ $divisi->id }}">{{ $divisi->nama }}</option>
                @endforeach
              </select>
            </div>

            <div id="divisi-error">
              @error('divisi_id')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Unit</label>
            <input type="text" wire:model.defer="nama"
              class="form-control text-uppercase @error('nama') is-invalid @enderror" autocomplete="off" placeholder="nama unit">
            @error('nama')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Kode</label>
            <input type="text" wire:model.defer="kode" class="form-control text-uppercase @error('kode') is-invalid @enderror" autocomplete="off" placeholder="kode unit">
            @error('kode')
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