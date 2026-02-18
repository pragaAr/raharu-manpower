<div>

  @include('livewire.karyawan.partials.header')

  {{-- FORM --}}
  <div class="page-body">
    <div class="container-xl px-0">
      <div class="row row-deck row-cards">

        <div class="col-12">
          <form class="card" id="mutasi-form" wire:submit.prevent="save">

            <div class="card-body">
              <div class="row g-3">

                <div class="col-md-12 mb-2">
                  <label for="mutasi-karyawanSelect" class="form-label">Karyawan</label>
                  <input type="hidden" id="mutasi-karyawanHidden" wire:model.live="karyawanId">
                  <div wire:ignore>
                    <select id="mutasi-karyawanSelect" class="form-select">
                      @foreach ($karyawans as $karyawan)
                      <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="mutasi-karyawanError">
                    @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-6 col-sm-6">
                  <p class="text-secondary mb-1">Data Lama Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <label for="kategoriOld" class="form-label">Kategori</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['kategori'] }}" id="kategoriOld" placeholder="Kategori" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="lokasiOld" class="form-label">Penempatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['lokasi'] }}"  id="lokasiOld" placeholder="Penempatan" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="jabatanOld" class="form-label">Jabatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['jabatan'] }}" id="jabatanOld" placeholder="Jabatan" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="divisiOld" class="form-label">Divisi</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['divisi'] }}" id="divisiOld"  placeholder="Divisi" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="unitOld" class="form-label">Unit</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['unit'] }}" id="unitOld"  placeholder="Unit" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="efektifOld" class="form-label">Efektif</label>
                      <input type="date" class="form-control" value="{{ $old['efektif']?->format('Y-m-d') }}" id="efektifOld" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="tmkOld" class="form-label">TMK</label>
                      <input type="date" class="form-control" value="{{ $old['tmk']?->format('Y-m-d') }}" id="tmkOld" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="thkOld" class="form-label">THK</label>
                      <input type="date" class="form-control" value="{{ $old['thk']?->format('Y-m-d') }}" id="thkOld" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="penetapanOld" class="form-label">Penetapan</label>
                      <input type="date" class="form-control" value="{{ $old['penetapan']?->format('Y-m-d') }}" id="penetapanOld" autocomplete="off" readonly disabled>
                    </div>
                  </div>

                </div>

                <div class="col-md-6 col-sm-6">
                  <p class="text-secondary mb-1">Data Baru Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <label for="mutasi-kategoriSelect" class="form-label">Kategori</label>
                      <input type="hidden" id="mutasi-kategoriHidden" wire:model.live="kategoriId">
                      <div wire:ignore>
                        <select id="mutasi-kategoriSelect" class="form-select">
                          @foreach ($kategoris as $kategori)
                          <option value="{{ $kategori->id }}">{{ strtoupper($kategori->nama) }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div id="mutasi-kategoriError">
                        @error('kategoriId') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="mutasi-lokasiSelect" class="form-label">Penempatan</label>
                      <input type="hidden" id="mutasi-lokasiHidden" wire:model.live="lokasiId">
                      <div wire:ignore>
                        <select id="mutasi-lokasiSelect" class="form-select">
                          @foreach ($lokasis as $lokasi)
                          <option value="{{ $lokasi->id }}">{{ strtoupper($lokasi->nama) }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div id="mutasi-lokasiError">
                        @error('lokasiId') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="mutasi-jabatanSelect" class="form-label">Jabatan</label>
                      <input type="hidden" id="mutasi-jabatanHidden" wire:model.live="jabatanId">
                      <div wire:ignore>
                        <select id="mutasi-jabatanSelect" class="form-select">
                          @foreach ($jabatans as $jabatan)
                          <option value="{{ $jabatan->id }}">{{ strtoupper($jabatan->unit_nama) }} - {{ strtoupper($jabatan->nama) }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div id="mutasi-jabatanError">
                        @error('jabatanId') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="divisi" class="form-label">Divisi</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $new['divisi'] }}" id="divisi" placeholder="Divisi" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label for="unit" class="form-label">Unit</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $new['unit'] }}" id="unit" placeholder="Unit" autocomplete="off" readonly disabled>
                    </div>

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
                      <label for="penetapan" class="form-label">Penetapan</label>
                      <input type="date" class="form-control @error('new.penetapan') is-invalid @enderror" wire:model="new.penetapan" id="penetapan" autocomplete="off">
                      @error('new.penetapan')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>
                   </div>
                </div>

                <div class="col-md-12 mb-2">
                  <p class="text-secondary mb-1">Catatan lain-lain</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <label for="keterangan" class="form-label">Keterangan</label>
                      <input type="text" class="form-control @error('keterangan') is-invalid @enderror" wire:model="keterangan" id="keterangan" placeholder="Keterangan" autocomplete="off">
                      @error('keterangan')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 my-2">
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
    window.__plugins.mutasi =
      createTomSelectGroup('#mutasi-form', [
        {
          selectId: 'mutasi-karyawanSelect',
          errorId: 'mutasi-karyawanError',
          hiddenInputId: 'mutasi-karyawanHidden',
          placeholder: 'Pilih Karyawan..'
        },
        {
          selectId: 'mutasi-kategoriSelect',
          errorId: 'mutasi-kategoriError',
          hiddenInputId: 'mutasi-kategoriHidden',
          placeholder: 'Pilih Kategori..'
        },
        {
          selectId: 'mutasi-lokasiSelect',
          errorId: 'mutasi-lokasiError',
          hiddenInputId: 'mutasi-lokasiHidden',
          placeholder: 'Pilih Penempatan..'
        },
        {
          selectId: 'mutasi-jabatanSelect',
          errorId: 'mutasi-jabatanError',
          hiddenInputId: 'mutasi-jabatanHidden',
          placeholder: 'Pilih Jabatan..'
        }
      ])
  })

  document.addEventListener('livewire:navigating', () => {
    window.__plugins.mutasi?.destroy()
    delete window.__plugins.mutasi
  })

  Livewire.on('reset-select', () => {
    window.__plugins.mutasi?.reset()
  })

  Livewire.on('refresh-tomselect', (data) => {
    const karyawans = data.karyawans || data[0]?.karyawans || []

    window.__plugins.mutasi?.refresh(
      'mutasi-karyawanSelect',
      karyawans,
      (k) => `${k.nik.toUpperCase()} - ${k.nama.toUpperCase()}`
    )
  })
</script>
@endpush