  <div wire:ignore.self 
    x-show="showImageModal" 
    x-transition.opacity
    class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center" 
    :class="showImageModal ? 'd-flex' : 'd-none'"
    style="z-index: 9999; background: rgba(0,0,0,0.85);" 
    x-cloak
    @click="showImageModal = false"
    @keydown.escape.window="showImageModal = false"
  >
    <div class="position-relative" @click.stop>
      <button 
        type="button" 
        class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
        style="z-index: 10000;" 
        @click="showImageModal = false"
      ></button>
      
      <img 
        :src="'{{ asset($karyawan->img) }}'" 
        wire:ignore
        class="img-fluid rounded shadow-lg" 
        style="max-width: 90vw; max-height: 90vh; object-fit: contain;"
      >
      
      <div class="text-center mt-2 text-light fw-bold text-uppercase">
        {{ $karyawan->nama }}
      </div>
    </div>
  </div>