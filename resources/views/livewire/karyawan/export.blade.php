{{-- Export Modal --}}
<style>
  #exportModal.show {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }
  
  #exportModal .modal-dialog {
    z-index: 1060;
  }
</style>

<div wire:ignore.self class="modal fade" id="exportModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Export Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <p class="mb-3">Pilih format export</p>
        
        <div class="d-flex gap-2">
          <button type="button" 
          wire:click="exportExcel"
          wire:loading.attr="disabled"
          wire:target="exportExcel"
          class="btn btn-outline-success w-50">
            <svg 
              wire:loading.remove
              wire:target="exportExcel"
              xmlns="http://www.w3.org/2000/svg" 
              width="24" 
              height="24" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2" 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel m-0">
              <path stroke="none" 
                d="M0 0h24v24H0z" 
                fill="none"/>
              <path d="M14 3v4a1 1 0 0 0 1 1h4" />
              <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
              <path d="M10 12l4 5" />
              <path d="M10 17l4 -5" />
            </svg>
            <span 
              wire:loading 
              wire:target="exportExcel" 
              class="spinner-border spinner-custom">
            </span>
            <span class="ms-1">Export Excel</span>
          </button>
          
          <button type="button"
            wire:click="exportPdf"
            class="btn btn-outline-danger w-50 d-inline-flex align-items-center justify-content-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icon-tabler-file-type-pdf m-0">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M14 3v4a1 1 0 0 0 1 1h4" />
              <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
              <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
              <path d="M17 18h2" />
              <path d="M20 15h-3v6" />
              <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1" />
            </svg>

            <span class="ms-1">Preview PDF</span>
          </button>

        </div>
      </div>

    </div>
  </div>
</div>
