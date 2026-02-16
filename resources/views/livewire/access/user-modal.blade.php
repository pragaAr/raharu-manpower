<div wire:ignore.self class="modal fade" id="addEditModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $isEdit ? 'Edit User' : 'Tambah User' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form wire:submit.prevent="save" class="row g-2">
          <div class="col-md-12">
            <label for="karyawan-select" class="form-label">Karyawan</label>
            <input type="hidden" id="karyawan-hidden" wire:model="karyawan_id">
            <div wire:ignore>
              <select id="karyawan-select" class="form-select text-uppercase">
                @foreach($karyawans as $k)
                <option value="{{ $k->id }}">{{ strtoupper($k->nik) }} - {{ $k->nama }}</option>
                @endforeach
              </select>
            </div>
            <div id="karyawan-error">
              @error('karyawan_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>
          <div class="col-md-12">
            <label for="username" class="form-label">Username</label>
            <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" id="username" autocomplete="off">
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-12">
            <label for="role-select" class="form-label">Role</label>
            <input type="hidden" id="role-hidden" wire:model="role_id">
            <div wire:ignore>
              <select id="role-select" class="form-select text-uppercase">
                @foreach($roles as $r)
                  <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
              </select>
            </div>
            <div id="role-error">
              @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>
          
          @if($showCustomPermissions)
          <div class="col-md-12">
            <label class="form-label">Custom Access</label>

            <div class="row" style="max-height: 300px; overflow-y: auto;">
              @foreach($groupedPermissions as $module => $items)
                <div class="col-12 mb-3">
                  <div class="hr-text fs-5 mt-2 mb-3">{{ $module }}</div>

                  <div class="row">
                    @foreach($items as $p)
                      @continue(in_array($p->id, $rolePermissions))

                      <div class="col-md-3 col-sm-3 mb-2">
                        <label class="form-check">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            wire:model.live="selectedPermissions"
                            value="{{ (string) $p->id }}"
                          >
                          <span class="form-check-label">
                            {{ ucfirst($p->label ? explode(' ', $p->label)[0] : (explode('.', $p->name)[1] ?? $p->name)) }}
                          </span>
                        </label>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @endif

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