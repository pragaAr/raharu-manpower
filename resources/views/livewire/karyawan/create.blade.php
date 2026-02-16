<div>

  @include('livewire.karyawan.partials.header')

  {{-- FORM --}}
  <div class="page-body">
    <div class="container-xl px-0">
      <div class="row row-deck row-cards">

        <div class="col-12">
          <form class="card" wire:submit.prevent="save">

            <div class="card-body">

              <p class="text-secondary mb-1">Identitas</p>
              <hr class="mt-2 mb-3">

              <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="nama" class="form-label">Nama</label>
                  <input type="text" class="form-control text-uppercase @error('nama') is-invalid @enderror" wire:model="nama" id="nama" placeholder="Nama lengkap" autocomplete="off" autofocus>
                  @error('nama')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="telpon" class="form-label">Telpon</label>
                  <input type="text" class="form-control @error('telpon') is-invalid @enderror" wire:model="telpon" id="telpon" placeholder="Telpon" autocomplete="off">
                  @error('telpon')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="ktp" class="form-label">KTP</label>
                  <input type="text" class="form-control @error('ktp') is-invalid @enderror" wire:model="ktp" id="ktp" placeholder="KTP" autocomplete="off">
                  @error('ktp')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="tglLahir" class="form-label">Tanggal Lahir</label>
                  <input type="date" class="form-control @error('tglLahir') is-invalid @enderror" wire:model="tglLahir" id="tglLahir" autocomplete="off">
                  @error('tglLahir')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6">
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

                  @error('jk')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="agama" class="form-label">Agama</label>
                  <input type="text" class="form-control text-uppercase @error('agama') is-invalid @enderror" wire:model="agama" id="agama" placeholder="Agama" autocomplete="off">
                  @error('agama')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="pendidikan" class="form-label">Pendidikan</label>
                  <input type="text" class="form-control text-uppercase @error('pendidikan') is-invalid @enderror" wire:model="pendidikan" id="pendidikan" placeholder="Pendidikan" autocomplete="off">
                  @error('pendidikan')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="marital" class="form-label">Marital</label>
                  <input type="text" class="form-control text-uppercase @error('marital') is-invalid @enderror" wire:model="marital" id="marital" placeholder="Marital" autocomplete="off">
                  @error('marital')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-12">
                  <label for="alamat" class="form-label">Alamat</label>
                  <input type="text" class="form-control text-uppercase @error('alamat') is-invalid @enderror" wire:model="alamat" id="alamat" placeholder="Alamat lengkap" autocomplete="off">
                  @error('alamat')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

              </div>

              <p class="text-secondary mb-1">Informasi pekerjaan</p>
              <hr class="mt-2 mb-3">

              <div class="row mb-3">
                <div class="col-md-4 col-sm-6 mb-2">
                  <label for="kategori-select" class="form-label">Kategori</label>
                  <input type="hidden" id="kategori-hidden" wire:model="kategori_id">
                  <div wire:ignore>
                    <select id="kategori-select" class="form-select">
                      @foreach ($kategoris as $kategori)
                      <option value="{{ $kategori->id }}">{{ strtoupper($kategori->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="kategori-error">
                    @error('kategori_id') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-4 col-sm-6 mb-2">
                  <label for="lokasi-select" class="form-label">Penempatan</label>
                  <input type="hidden" id="lokasi-hidden" wire:model="lokasi_id">
                  <div wire:ignore>
                    <select id="lokasi-select" class="form-select">
                      @foreach ($lokasis as $lokasi)
                      <option value="{{ $lokasi->id }}">{{ strtoupper($lokasi->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="lokasi-error">
                    @error('lokasi_id') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-4 col-sm-12 mb-2">
                  <label for="jabatan-select" class="form-label">Jabatan</label>
                  <input type="hidden" id="jabatan-hidden" wire:model="jabatan_id">
                  <div wire:ignore>
                    <select id="jabatan-select" class="form-select">
                      @foreach ($jabatans as $jabatan)
                      <option value="{{ $jabatan->id }}">{{ strtoupper($jabatan->unit_nama) }} - {{ strtoupper($jabatan->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="jabatan-error">
                    @error('jabatan_id') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-4 col-sm-6 mb-2">
                  <label for="tglMasuk" class="form-label">Masuk</label>
                  <input type="date" id="tglMasuk" class="form-control @error('tglMasuk') is-invalid @enderror" wire:model="tglMasuk" id="tglMasuk" autocomplete="off">
                  @error('tglMasuk')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-4 col-sm-6 mb-2">
                  <label for="tglEfektif" class="form-label">Efektif</label>
                  <input type="date" id="tglEfektif" class="form-control @error('tglEfektif') is-invalid @enderror" wire:model="tglEfektif" id="tglEfektif" autocomplete="off">
                  @error('tglEfektif')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-4 col-sm-6 mb-2">
                  <label for="tglPenetapan" class="form-label">Penetapan</label>
                  <input type="date" id="tglPenetapan" class="form-control @error('tglPenetapan') is-invalid @enderror" wire:model="tglPenetapan" id="tglPenetapan" autocomplete="off">
                  @error('tglPenetapan')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-6 col-sm-6 mb-2">
                  <label for="tglMulai" class="form-label">TMK</label>
                  <input type="date" id="tglMulai" class="form-control @error('tglMulai') is-invalid @enderror {{ isset($kontrakErrors['tglMulai']) ? 'is-invalid' : '' }}" wire:model="tglMulai" id="tglMulai" autocomplete="off">
                  @error('tglMulai')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                  @if(isset($kontrakErrors['tglMulai']))
                  <small class="text-danger">TMK wajib diisi untuk kontrak</small>
                  @endif
                </div>

                <div class="col-md-6 col-sm-6 mb-2">
                  <label for="tglSelesai" class="form-label">THK</label>
                  <input type="date" id="tglSelesai" class="form-control @error('tglSelesai') is-invalid @enderror {{ isset($kontrakErrors['tglSelesai']) ? 'is-invalid' : '' }}" wire:model="tglSelesai" id="tglSelesai" autocomplete="off">
                  @error('tglSelesai')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                  @if(isset($kontrakErrors['tglSelesai']))
                  <small class="text-danger">THK wajib diisi untuk kontrak</small>
                  @endif
                </div>

              </div>

              <p class="text-secondary mb-1">Lain-lain</p>
              <hr class="mt-2 mb-3">

              <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="bpjsTk" class="form-label">BPJS TK</label>
                  <input type="text" class="form-control text-uppercase" wire:model="bpjsTk" id="bpjsTk" placeholder="Ketenagakerjaan" autocomplete="off">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                  <label for="bpjsKs" class="form-label">BPJS KS</label>
                  <input type="text" class="form-control text-uppercase" wire:model="bpjsKs" id="bpjsKs" placeholder="Kesehatan" autocomplete="off">
                </div>

                <div class="col-md-6 col-sm-12 mb-2">
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

                <div class="col-12 mb-2">
                  <label for="keterangan" class="form-label">Keterangan</label>
                  <input type="text" class="form-control text-uppercase" wire:model="keterangan" id="keterangan" placeholder="Keterangan" autocomplete="off">
                </div>

                <div class="col-md-12 my-2">
                  <button class="btn btn-primary w-100" type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="save, fotoUpload">
                    <span wire:loading.remove wire:target="save, fotoUpload">Simpan</span>
                    <span wire:loading wire:target="fotoUpload">
                      <span class="spinner-border spinner-border-sm me-2"></span> Mengupload foto...
                    </span>
                    <span wire:loading wire:target="save">
                      <span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...
                    </span>
                  </button>
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
  document.addEventListener('livewire:navigated', () => {
    const dateFields = ['tglLahir', 'tglMulai', 'tglMasuk', 'tglSelesai', 'tglPenetapan'];
    initDatePickers(dateFields);

    const kategoriSelectEl = document.getElementById('kategori-select')
    const lokasiSelectEl = document.getElementById('lokasi-select')
    const jabatanSelectEl = document.getElementById('jabatan-select')

    const kategoriErrContainer = document.getElementById('kategori-error')
    const lokasiErrContainer = document.getElementById('lokasi-error')
    const jabatanErrContainer = document.getElementById('jabatan-error')

    if (!kategoriSelectEl || !lokasiSelectEl || !jabatanSelectEl) return

    const observeError = (selectEl, errEl) => {
      if (!errEl) return

      const observer = new MutationObserver(() => {
        if (!selectEl.tomselect) return
        selectEl.tomselect.wrapper.classList.toggle(
          'is-invalid',
          errEl.querySelector('small') !== null
        )
      })

      observer.observe(errEl, {
        childList: true,
        subtree: true
      })
    }

    observeError(kategoriSelectEl, kategoriErrContainer)
    observeError(lokasiSelectEl, lokasiErrContainer)
    observeError(jabatanSelectEl, jabatanErrContainer)

    const initTomSelect = (selectEl, hiddenInputId, placeholder) => {
      if (selectEl.tomselect) {
        selectEl.tomselect.destroy()
      }

      const hiddenInput = document.getElementById(hiddenInputId)
      
      const ts = new TomSelect(selectEl, {
        allowEmptyOption: true,
        placeholder: placeholder,
        items: [], // Tidak ada item terpilih secara default, hanya tampilkan placeholder
        onChange(value) {
          // Set hidden input value dan trigger input event untuk Livewire
          hiddenInput.value = value
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }))
        }
      })

      // Prevent Enter key from submitting form
      ts.control_input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault()
          e.stopPropagation()
        }
      })

      return ts
    }

    initTomSelect(kategoriSelectEl, 'kategori-hidden', 'Pilih Kategori..')
    initTomSelect(lokasiSelectEl, 'lokasi-hidden', 'Pilih Penempatan..')
    initTomSelect(jabatanSelectEl, 'jabatan-hidden', 'Pilih Jabatan..')

    document.addEventListener('resetSelect', () => {
      document.querySelectorAll('select').forEach(select => {
        if (select.tomselect) {
          select.tomselect.clear()
        }
      })
    })

    Livewire.on('focusFirstInput', () => {
      const el = document.querySelector('input[wire\\:model="nama"]');
      if (el) setTimeout(() => el.focus(), 100);
    });

  })
</script>
@endpush