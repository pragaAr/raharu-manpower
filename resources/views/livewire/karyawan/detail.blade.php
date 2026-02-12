<div x-data="{ 
    showImageModal: false,
    tab: $wire.entangle('activeTab')
  }">

  @include('livewire.karyawan.partials.header')

  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs">
        <li class="nav-item">
          <a 
            href="javascript:void(0)" 
            @click.prevent="tab = 'informasi'"
            :class="{ 'nav-link': true, 'active': tab === 'informasi' }"
            class="nav-link {{ $activeTab === 'informasi' ? 'active' : '' }}"
          >
            <svg xmlns="http://www.w3.org/2000/svg" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24" 
            fill="currentColor" 
            class="icon icon-tabler icons-tabler-filled icon-tabler-info-circle me-1">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
            </svg>
            Informasi
          </a>
        </li>
        <li class="nav-item">
          <a 
            href="javascript:void(0)" 
            @click.prevent="tab = 'history'"
            :class="{ 'nav-link': true, 'active': tab === 'history' }"
            class="nav-link {{ $activeTab === 'history' ? 'active' : '' }}"
          >
            <svg xmlns="http://www.w3.org/2000/svg" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            class="icon icon-tabler icons-tabler-outline icon-tabler-history me-1">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 8l0 4l2 2" />
              <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
            </svg>
            Riwayat
          </a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content">
        <div class="tab-pane {{ $activeTab === 'informasi' ? 'active show' : '' }}" :class="{ 'active show': tab === 'informasi' }" wire:ignore>
          <div class="row row-cards">
            @include('livewire.karyawan.partials.profile-card')
            @include('livewire.karyawan.partials.identity-card')
            @include('livewire.karyawan.partials.employment-card')
          </div>
        </div>
        <div class="tab-pane" :class="{ 'active show': tab === 'history' }">
          @include('livewire.karyawan.partials.history-card')
        </div>
      </div>
    </div>
  </div>

  @include('livewire.karyawan.image')

</div>
