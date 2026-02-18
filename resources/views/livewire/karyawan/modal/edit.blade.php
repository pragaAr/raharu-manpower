<div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Edit Karyawan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="update">
          <p class="text-secondary mb-1">Identitas</p>
          <hr class="mt-2 mb-3">

          <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="nama" class="form-label">Nama</label>
              <input type="text" class="form-control text-uppercase @error('nama') is-invalid @enderror" wire:model="nama" id="nama" placeholder="Nama lengkap" autocomplete="off" autofocus>
              @error('nama')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="telpon" class="form-label">Telpon</label>
              <input type="text" class="form-control @error('telpon') is-invalid @enderror" wire:model="telpon" id="telpon" placeholder="Telpon" autocomplete="off">
              @error('telpon')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="ktp" class="form-label">KTP</label>
              <input type="text" class="form-control @error('ktp') is-invalid @enderror" wire:model="ktp" id="ktp" placeholder="KTP" autocomplete="off">
              @error('ktp')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="tglLahir" class="form-label">Tanggal Lahir</label>
              <input type="date" class="form-control @error('tglLahir') is-invalid @enderror" wire:model="tglLahir" id="tglLahir" autocomplete="off">
              @error('tglLahir')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <div class="form-label">Jenis Kelamin</div>

              <div class="pt-2" style="padding: 0 0.5rem;">
                <label class="form-check form-check-inline mx-1">
                  <input
                    class="form-check-input @error('jk') is-invalid @enderror"
                    type="radio"
                    wire:model="jk"
                    value="l">
                  <span class="form-check-label">L</span>
                </label>

                <label class="form-check form-check-inline mx-1">
                  <input
                    class="form-check-input @error('jk') is-invalid @enderror"
                    type="radio"
                    wire:model="jk"
                    value="p">
                  <span class="form-check-label">P</span>
                </label>
              </div>

              @error('jenisKelamin')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="agama" class="form-label">Agama</label>
              <input type="text" class="form-control text-uppercase @error('agama') is-invalid @enderror" wire:model="agama" id="agama" placeholder="Agama" autocomplete="off">
              @error('agama')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="pendidikan" class="form-label">Pendidikan</label>
              <input type="text" class="form-control text-uppercase @error('pendidikan') is-invalid @enderror" wire:model="pendidikan" id="pendidikan" placeholder="Pendidikan" autocomplete="off">
              @error('pendidikan')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="marital" class="form-label">Marital</label>
              <input type="text" class="form-control text-uppercase @error('marital') is-invalid @enderror" wire:model="marital" id="marital" placeholder="Marital" autocomplete="off">
              @error('marital')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-12">
              <label for="alamat" class="form-label">Alamat</label>
              <input type="text" class="form-control text-uppercase @error('alamat') is-invalid @enderror" wire:model="alamat" id="alamat" placeholder="Alamat lengkap" autocomplete="off">
              @error('alamat')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

          </div>

          <p class="text-secondary mb-1">Lain-lain</p>
          <hr class="mt-2 mb-3">

          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="bpjsTk" class="form-label">BPJS TK</label>
              <input type="text" class="form-control text-uppercase" wire:model="bpjsTk" id="bpjsTk" placeholder="Ketenagakerjaan" autocomplete="off">
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
              <label for="bpjsKs" class="form-label">BPJS KS</label>
              <input type="text" class="form-control text-uppercase" wire:model="bpjsKs" id="bpjsKs" placeholder="Kesehatan" autocomplete="off">
            </div>

            <div class="col-lg-6 col-sm-12 mb-2">
              <label for="fotoUpload" class="form-label">Foto</label>
              <input type="file"
                class="form-control @error('fotoUpload') is-invalid @enderror"
                wire:model="fotoUpload"
                accept="image/png, image/jpeg, image/jpg, image/webp" id="fotoUpload">
              
              {{-- Error Validation --}}
              @error('fotoUpload')
              <small class="text-danger">{{ $message }}</small>
              @enderror

              {{-- Upload Indicator --}}
              <div wire:loading wire:target="fotoUpload" class="mt-2">
                <span class="spinner-border spinner-border-sm text-primary me-1"></span>
                <small class="text-muted">Mengupload foto...</small>
              </div>

              {{-- Preview setelah upload sukses --}}
              @if ($fotoUpload && !$errors->has('fotoUpload'))
              <div class="mt-2" wire:loading.remove wire:target="fotoUpload">
                <small class="text-success">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                    <path d="M5 12l5 5l10 -10" />
                  </svg>
                  Foto ter-upload
                </small>
              </div>
              @endif
            </div>

          </div>

          <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="update, fotoUpload">
            <span wire:loading.remove wire:target="update, fotoUpload">Update</span>
            <span wire:loading wire:target="fotoUpload">
              <span class="spinner-border spinner-border-sm me-2"></span> Mengupload foto...
            </span>
            <span wire:loading wire:target="update">
              <span class="spinner-border spinner-border-sm me-2"></span> Mengupdate...
            </span>
          </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>