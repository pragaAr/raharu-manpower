<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">{{ $pretitle }}</div>
        <h2 class="page-title">{{ $title }}</h2>
      </div>

      <div class="col-auto ms-auto d-print-none">

        <a
          x-data="{ isLoading: false }"
          href="{{ $backUrl }}"
          @click="isLoading = true"
          class="btn btn-dark btn-sm-custom">

          <svg 
            x-show="!isLoading" 
            xmlns="http://www.w3.org/2000/svg" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-left m-0">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l14 0" />
            <path d="M5 12l4 4" />
            <path d="M5 12l4 -4" />
          </svg>

          <span
            x-show="isLoading"
            x-cloak
            class="spinner-border spinner-custom">
          </span>

          <span class="d-none d-sm-inline ms-1">Kembali</span>
        </a>

      </div>
    </div>
  </div>