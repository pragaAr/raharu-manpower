<div wire:ignore.self class="modal fade" id="filterModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Filter Data Karyawan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form wire:submit.prevent="filter" class="row g-2">

          <div class="col-md-12 mb-2">
            <label for="kategori-filter-select" class="form-label">Kategori</label>
            <input type="hidden" id="kategori-filter-hidden" wire:model.defer="draft.kategori_id">
            <div wire:ignore>
              <select id="kategori-filter-select" class="form-select">
                @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}">{{ strtoupper($kategori->nama) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          @role('Administrator')
          <div class="col-md-12 mb-2">
            <label for="lokasi-filter-select" class="form-label">Penempatan</label>
            <input type="hidden" id="lokasi-filter-hidden" wire:model.defer="draft.lokasi_id">
            <div wire:ignore>
              <select id="lokasi-filter-select" class="form-select">
                @foreach ($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}">{{ strtoupper($lokasi->nama) }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @endrole
          
          <div class="col-md-12 mb-2">
            <label for="divisi-filter-select" class="form-label">Divisi</label>
            <input type="hidden" id="divisi-filter-hidden" wire:model.defer="draft.divisi_id">
            <div wire:ignore>
              <select id="divisi-filter-select" class="form-select">
                @foreach ($divisis as $divisi)
                <option value="{{ $divisi->id }}">{{ strtoupper($divisi->nama) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-12 mb-2">
            <label for="tgl-masuk-start" class="form-label">Tanggal Masuk (Dari)</label>
            <input 
              type="date" 
              id="tgl-masuk-start" 
              class="form-control" 
              wire:model.defer="draft.tgl_masuk_start">
          </div>

          <div class="col-md-12 mb-2">
            <label for="tgl-masuk-end" class="form-label">Tanggal Masuk (Sampai)</label>
            <input 
              type="date" 
              id="tgl-masuk-end" 
              class="form-control" 
              wire:model.defer="draft.tgl_masuk_end">
          </div>

          <div class="col-md-12 mb-2">
            <div class="form-label">Status</div>

            <div class="pt-2" style="padding: 0 0.5rem;">
              <label class="form-check form-check-inline mx-1">
                <input
                  class="form-check-input"
                  type="radio"
                  wire:model.defer="draft.status"
                  value="aktif">
                <span class="form-check-label">Aktif</span>
              </label>

              <label class="form-check form-check-inline mx-1">
                <input
                  class="form-check-input"
                  type="radio"
                  wire:model.defer="draft.status"
                  value="nonaktif">
                <span class="form-check-label">Nonaktif</span>
              </label>

              <label class="form-check form-check-inline mx-1">
                <input
                  class="form-check-input"
                  type="radio"
                  wire:model.defer="draft.status"
                  value="vakum">
                <span class="form-check-label">Vakum</span>
              </label>
            </div>
          </div>

          <div class="col-md-12 mt-3">
            @if($this->hasActiveFilters())
              {{-- Filter SUDAH diaplikasikan --}}
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill"
                  wire:click.prevent="resetFilter"
                  wire:loading.attr="disabled"
                  wire:target="resetFilter">
                  Reset
                </button>

                <button type="submit" class="btn btn-primary flex-fill"
                  wire:loading.attr="disabled"
                  wire:target="filter">
                  Filter
                  <span wire:loading wire:target="filter" class="spinner-border spinner-border-sm ms-2"></span>
                </button>

                <button type="button" class="btn btn-success flex-fill"
                  wire:click="openExport"
                  wire:loading.attr="disabled"
                  wire:target="openExport">
                  Export
                </button>
              </div>
            @else
              {{-- Belum ada filter aktif --}}
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-50"
                  wire:loading.attr="disabled"
                  wire:target="filter">
                  Filter
                  <span wire:loading wire:target="filter" class="spinner-border spinner-border-sm ms-2"></span>
                </button>

                <button type="button" class="btn btn-success w-50"
                  wire:click="openExport"
                  wire:loading.attr="disabled"
                  wire:target="openExport">
                  Export
                </button>
              </div>
            @endif
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

