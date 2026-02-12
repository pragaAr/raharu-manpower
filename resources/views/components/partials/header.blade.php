  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Data</div>
        <h2 class="page-title">{{ $title }}</h2>
      </div>

      @isset($permission)
      @can($permission)
      <div class="col-auto ms-auto d-print-none">
        <button type="button"
          wire:click="create"
          wire:loading.attr="disabled"
          wire:target="create"
          class="btn btn-primary btn-sm-custom">

          <svg 
            wire:loading.remove 
            wire:target="create" 
            xmlns="http://www.w3.org/2000/svg" 
            class="icon m-0" 
            width="24" height="24" 
            viewBox="0 0 24 24" 
            stroke-width="2" 
            stroke="currentColor" 
            fill="none" 
            stroke-linecap="round" 
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
          </svg>

          <span
            wire:loading
            wire:target="create"
            class="spinner-border spinner-custom">
          </span>
          
          <span class="d-none d-sm-inline ms-1">Tambah</span>
        </button>
      </div>
      @endcan
      @else
      <div class="col-auto ms-auto d-print-none">
        <button type="button"
          wire:click="create"
          wire:loading.attr="disabled"
          wire:target="create"
          class="btn btn-primary btn-sm-custom">

          <svg 
            wire:loading.remove 
            wire:target="create" 
            xmlns="http://www.w3.org/2000/svg" 
            class="icon m-0" 
            width="24" height="24" 
            viewBox="0 0 24 24" 
            stroke-width="2" 
            stroke="currentColor" 
            fill="none" 
            stroke-linecap="round" 
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
          </svg>

          <span
            wire:loading
            wire:target="create"
            class="spinner-border spinner-custom">
          </span>
          
          <span class="d-none d-sm-inline ms-1">Tambah</span>
        </button>
      </div>
      @endisset

    </div>
  </div>