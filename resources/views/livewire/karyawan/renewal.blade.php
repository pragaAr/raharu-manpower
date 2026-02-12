<div>

  @include('livewire.karyawan.partials.header')

  <div class="page-body">
    <div class="container-xl px-0">
      <div class="row row-deck row-cards">

        <div class="col-12">
          <form class="card" wire:submit.prevent="save">

            <div class="card-body">
              <div class="row g-3">

                <div class="col-md-12 mb-2">
                  <label class="form-label">Karyawan</label>
                  <input type="hidden" id="karyawan-hidden" wire:model.live="karyawanId">
                  <div wire:ignore>
                    <select id="karyawan-select" class="form-select">
                      @foreach ($karyawans as $karyawan)
                      <option value="{{ $karyawan->id }}">{{ strtoupper($karyawan->nik) }} - {{ strtoupper($karyawan->nama) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="karyawan-error">
                    @error('karyawanId') <small class="text-danger">{{ $message }}</small> @enderror
                  </div>
                </div>

                <div class="col-md-6 col-sm-6 ">
                  <p class="text-secondary mb-1">Data kontrak Lama Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-6 col-sm-6 mb-2">
                      <label class="form-label">Penempatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['lokasi'] }}" placeholder="Penempatan" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label class="form-label">Jabatan</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['jabatan'] }}" placeholder="Jabatan" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label class="form-label">Divisi</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['divisi'] }}" placeholder="Divisi" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                      <label class="form-label">Unit</label>
                      <input type="text" class="form-control text-uppercase" value="{{ $old['unit'] }}" placeholder="Unit" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">Efektif</label>
                      <input type="date" class="form-control" value="{{ $old['efektif'] }}" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">TMK</label>
                      <input type="date" class="form-control" value="{{ $old['tmk'] }}" autocomplete="off" readonly disabled>
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">THK</label>
                      <input type="date" class="form-control" value="{{ $old['thk'] }}" autocomplete="off" readonly disabled>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-sm-6">
                  <p class="text-secondary mb-1">Data Kontrak Baru Karyawan</p>
                  <hr class="mt-2 mb-3">

                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <label class="form-label">Efektif</label>
                      <input type="date" class="form-control @error('new.efektif') is-invalid @enderror" wire:model="new.efektif" id="efektif" autocomplete="off">
                      @error('new.efektif')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">TMK</label>
                      <input type="date" class="form-control @error('new.tmk') is-invalid @enderror" wire:model="new.tmk" id="tmk" autocomplete="off">
                      @error('new.tmk')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">THK</label>
                      <input type="date" class="form-control @error('new.thk') is-invalid @enderror" wire:model="new.thk" id="thk" autocomplete="off">
                      @error('new.thk')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>

                    <div class="col-md-12 mb-2">
                      <label class="form-label">Keterangan</label>
                      <input type="text" class="form-control @error('keterangan') is-invalid @enderror" wire:model="keterangan" placeholder="Keterangan" autocomplete="off">
                      @error('keterangan')
                      <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="col-md-12">
                      <label class="form-label invisible">space agar sejajar dengan input</label>

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
  document.addEventListener('livewire:navigated', () => {

    const dateFields = ['efektif', 'tmk', 'thk'];

    dateFields.forEach((id) => {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('click', function() {
          this.showPicker ? this.showPicker() : this.click();
        });
      }
    });

    const karyawanSelectEl = document.getElementById('karyawan-select')

    const karyawanErrContainer = document.getElementById('karyawan-error')

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

    observeError(karyawanSelectEl, karyawanErrContainer)

    const initTomSelect = (selectEl, hiddenInputId, placeholder) => {
      if (selectEl.tomselect) {
        selectEl.tomselect.destroy()
      }

      const ts = new TomSelect(selectEl, {
        allowEmptyOption: true,
        placeholder: placeholder,
        items: [], 
        onChange(value) {
          const hiddenInput = document.getElementById(hiddenInputId)

          hiddenInput.value = value
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }))
        }
      })

      ts.control_input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault()
          e.stopPropagation()
        }
      })

      return ts
    }

    initTomSelect(karyawanSelectEl, 'karyawan-hidden', 'Pilih Karyawan..')

    document.addEventListener('resetSelect', () => {
      document.querySelectorAll('select').forEach(select => {
        if (select.tomselect) {
          select.tomselect.clear()
        }
      })
    })

     Livewire.on('refresh-tomselect', (data) => {
      if (karyawanSelectEl?.tomselect) {
        karyawanSelectEl.tomselect.destroy()
      }

      karyawanSelectEl.innerHTML = ''

      const karyawans = data.karyawans || data[0]?.karyawans || []

      karyawans.forEach(k => {
        const option = document.createElement('option')
        option.value = k.id
        option.textContent = `${k.nik.toUpperCase()} - ${k.nama.toUpperCase()}`
        karyawanSelectEl.appendChild(option)
      })

      initTomSelect(karyawanSelectEl, 'karyawan-hidden', 'Pilih Karyawan..')
    })
  })
</script>
@endpush