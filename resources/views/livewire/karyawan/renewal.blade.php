<div>

  @include('livewire.karyawan.partials.header')

  <div class="page-body">
    <div class="container-xl px-0">
      <div class="row row-deck row-cards">

        <div class="col-12">
          <form class="card" id="renewal-form" wire:submit.prevent="save">

            <div class="card-body">
              <div class="row g-3">

                <div class="col-md-12 mb-2">
                  <label for="renewal-karyawanSelect" class="form-label">Karyawan</label>
                  <input type="hidden" id="renewal-karyawanHidden" wire:model.live="karyawanId">
                  <div wire:ignore>
                    <select id="renewal-karyawanSelect" class="form-select">
                      @foreach ($karyawans as $karyawan)
                      <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="renewal-karyawanError">
                    @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-6 col-sm-6 ">
                  <p class="text-secondary mb-1">Data kontrak Lama Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-6 col-sm-6 mb-2">
                      <label for="oldLokasi" class="form-label">Penempatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['lokasi'] }}" placeholder="Penempatan" autocomplete="off" id="oldLokasi" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label for="oldJabatan" class="form-label">Jabatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['jabatan'] }}" placeholder="Jabatan" autocomplete="off" id="oldJabatan" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label for="oldDivisi" class="form-label">Divisi</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['divisi'] }}" placeholder="Divisi" autocomplete="off" id="oldDivisi" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label for="oldUnit" class="form-label">Unit</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['unit'] }}" placeholder="Unit" autocomplete="off" id="oldUnit" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="oldEfektif" class="form-label">Efektif</label>
                      <input type="date" class="form-control" value="{{ $old['efektif'] }}" autocomplete="off" id="oldEfektif" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="oldTmk" class="form-label">TMK</label>
                      <input type="date" class="form-control" value="{{ $old['tmk'] }}" autocomplete="off" id="oldTmk" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="oldThk" class="form-label">THK</label>
                      <input type="date" class="form-control" value="{{ $old['thk'] }}" autocomplete="off" id="oldThk" readonly disabled>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-sm-6">
                  <p class="text-secondary mb-1">Data Kontrak Baru Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <label for="efektif" class="form-label">Efektif</label>
                      <input type="date" class="form-control @error('new.efektif') is-invalid @enderror" wire:model="new.efektif" id="efektif" autocomplete="off">
                      @error('new.efektif')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="tmk" class="form-label">TMK</label>
                      <input type="date" class="form-control @error('new.tmk') is-invalid @enderror" wire:model="new.tmk" id="tmk" autocomplete="off">
                      @error('new.tmk')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="thk" class="form-label">THK</label>
                      <input type="date" class="form-control @error('new.thk') is-invalid @enderror" wire:model="new.thk" id="thk" autocomplete="off">
                      @error('new.thk')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="keterangan" class="form-label">Keterangan</label>
                      <input type="text" class="form-control @error('keterangan') is-invalid @enderror" wire:model="keterangan" placeholder="Keterangan" id="keterangan" autocomplete="off">
                      @error('keterangan')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="col-md-12">
                      <div class="form-label invisible">space agar sejajar dengan input</div>

                      <button class="btn btn-primary w-100" type="submit" 
                        wire:loading.attr="disabled" 
                        wire:target="save">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">
                          <span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  window.__plugins = window.__plugins || {}

  document.addEventListener('livewire:navigated', () => {
    window.__plugins.renewal =
      createTomSelectGroup('#renewal-form', [
        {
          selectId: 'renewal-karyawanSelect',
          errorId: 'renewal-karyawanError',
          hiddenInputId: 'renewal-karyawanHidden',
          placeholder: 'Pilih Karyawan..'
        }
      ])
  })

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.renewal?.destroy()
    delete window.__plugins.renewal
  })

  Livewire.on('reset-select', () => {
    window.__plugins.renewal?.reset()
  })

  Livewire.on('refresh-tomselect', (data) => {
    const karyawans = data.karyawans || data[0]?.karyawans || []

    window.__plugins.renewal?.refresh(
      'renewal-karyawanSelect',
      karyawans,
      (k) => `${k.nik.toUpperCase()} - ${k.nama.toUpperCase()}`
    )
  })
</script>
@endpush