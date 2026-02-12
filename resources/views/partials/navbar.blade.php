<header class="navbar navbar-expand-md d-print-none sticky-top" wire:ignore>
  <div class="container-xl">

    <!-- Toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Logo -->
    <h1 class="navbar-brand navbar-brand-autodark d-none d-md-block pe-0 pe-md-3">
      <a href="{{ url('/home') }}" wire:navigate>
        <img src="{{ asset('img/raharu-light.png') }}"
          alt="Logo"
          class="navbar-brand-image"
          style="height:1.3rem;">
      </a>
    </h1>

    <!-- Right menu -->
    <div class="navbar-nav flex-row order-md-last">

      <!-- Theme toggle -->
      <button type="button" class="nav-link" id="themeMode">
        <span id="themeIcon"></span>
      </button>

      <!-- Notification -->
      <div class="nav-item dropdown me-3">
        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
          </svg>
          <span class="badge d-none" id="badgeNotification"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
          <div class="card">
            <div class="list-group list-group-flush list-group-hoverable">
              <div class="list-group-item p-3" style="width: 300px;">
                <div id="rowNotification"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Login user dropdown -->
      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
          <span class="avatar avatar-sm bg-transparent" style="background-image: url('{{ asset('uploads/default.webp') }}'); box-shadow: none;" id="userAvatar"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow text-capitalize">
          <p class="dropdown-item">{{ auth()->user()->karyawan->nama ?? auth()->user()->username }}</p>
          <div class="dropdown-divider m-1"></div>
          <a href="{{ route('logout') }}" class="dropdown-item text-danger">Logout</a>
        </div>
      </div>

    </div>

    <!-- MENU -->
    <div class="collapse navbar-collapse" id="navbar-menu">
      <div class="d-flex flex-column flex-fill align-items-stretch align-items-md-center">
        <ul class="navbar-nav">

          {{-- Home --}}
          <li class="nav-item {{ request()->segment(1) == 'home' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/home') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                  <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                  <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
              </span>
              <span class="nav-link-title">
                Home
              </span>
            </a>
          </li>

          {{-- Master Dropdown --}}
          @if(auth()->user()->canAny(['lokasi.view', 'divisi.view', 'unit.view', 'jabatan.view', 'kategori.view']))
          <li class="nav-item dropdown
          {{ in_array(request()->segment(1), ['lokasi','divisi','unit','jabatan', 'kategori']) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-stack-2">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M12 4l-8 4l8 4l8 -4l-8 -4" />
                  <path d="M4 12l8 4l8 -4" />
                  <path d="M4 16l8 4l8 -4" />
                </svg>
              </span>
              <span class="nav-link-title">Master</span>
            </a>

            <div class="dropdown-menu">
              @can('lokasi.view')
              <a class="dropdown-item {{ request()->segment(1) == 'lokasi' ? 'active' : '' }}" href="{{ url('/lokasi') }}" wire:navigate>
                - Lokasi
              </a>
              @endcan
              @can('divisi.view')
              <a class="dropdown-item {{ request()->segment(1) == 'divisi' ? 'active' : '' }}" href="{{ url('/divisi') }}" wire:navigate>
                - Divisi
              </a>
              @endcan
              @can('unit.view')
              <a class="dropdown-item {{ request()->segment(1) == 'unit' ? 'active' : '' }}" href="{{ url('/unit') }}" wire:navigate>
                - Unit
              </a>
              @endcan
              @can('jabatan.view')
              <a class="dropdown-item {{ request()->segment(1) == 'jabatan' ? 'active' : '' }}" href="{{ url('/jabatan') }}" wire:navigate>
                - Jabatan
              </a>
              @endcan
              @can('kategori.view')
              <a class="dropdown-item {{ request()->segment(1) == 'kategori' ? 'active' : '' }}" href="{{ url('/kategori') }}" wire:navigate>
                - Kategori
              </a>
              @endcan
            </div>
          </li>
          @endif

          {{-- Karyawan --}}
          @can('karyawan.view')
          <li class="nav-item {{ request()->segment(1) == 'karyawan' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/karyawan') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                  <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                  <path d="M15 19l2 2l4 -4" />
                </svg>
              </span>
              <span class="nav-link-title">
                Karyawan
              </span>
            </a>
          </li>
          @endcan

          {{-- Absensi --}}
          @can('absensi.view')
          <li class="nav-item {{ request()->segment(1) == 'absensi' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/absensi') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
               <svg xmlns="http://www.w3.org/2000/svg" 
                width="24" 
                height="24" 
                viewBox="0 0 24 24" 
                fill="none"   
                stroke="currentColor"   
                stroke-width="2"  
                stroke-linecap="round"   
                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-stats m-0"> 
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>  
                  <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />  
                  <path d="M18 14v4h4" /> 
                  <path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /> 
                  <path d="M15 3v4" /> 
                  <path d="M7 3v4" />  
                  <path d="M3 11h16" /> 
              </svg>
              </span>
              <span class="nav-link-title">
                Absensi
              </span>
            </a>
          </li>
          @endcan

          {{-- Permission Dropdown --}}
          @if(auth()->user()->canAny(['role.view', 'permission.view']))
          <li class="nav-item dropdown
          {{ in_array(request()->segment(1), ['role', 'permission']) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shield-lock">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                  <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                  <path d="M12 12l0 2.5" />
                </svg>
              </span>
              <span class="nav-link-title">Permission</span>
            </a>
            <div class="dropdown-menu">
              @can('permission.view')
              <a class="dropdown-item {{ request()->segment(1) == 'permission' ? 'active' : '' }}" href="{{ url('/permission') }}" wire:navigate>
                - Akses
              </a>
              @endcan
              @can('role.view')
              <a class="dropdown-item {{ request()->segment(1) == 'role' ? 'active' : '' }}" href="{{ url('/role') }}" wire:navigate>
                - Role
              </a>
              @endcan
            </div>
          </li>
          @endif

          {{-- User --}}
          @can('user.view')
          <li class="nav-item {{ request()->segment(1) == 'user' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/user') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
              </span>
              <span class="nav-link-title">
                Users
              </span>
            </a>
          </li>
          @endcan

        </ul>
      </div>
    </div>

  </div>
</header>

<script>
  (function() {
    var htmlEl, themeToggle, themeIcon;

    const sunIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
        stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l .7 .7m12.1 -.7l -.7 .7m0 11.4l .7 .7m-12.1 -.7l -.7 .7" />
      </svg>
    `;

    const moonIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg>
    `;

    function updateIcon(theme) {
      if (themeIcon) {
        // Jika sedang LIGHT mode, tampilkan icon MOON (untuk ke dark)
        // Jika sedang DARK mode, tampilkan icon SUN (untuk ke light)
        themeIcon.innerHTML = (theme === 'light') ? moonIcon : sunIcon;
      }
    }

    function initTheme() {
      htmlEl = document.documentElement;
      themeToggle = document.getElementById('themeMode');
      
      if (!themeToggle) return;

      const currentTheme = localStorage.getItem('theme') || 'light';
      htmlEl.setAttribute('data-bs-theme', currentTheme);

      // Prevent multiple listeners by cloning the element
      const newToggle = themeToggle.cloneNode(true);
      themeToggle.parentNode.replaceChild(newToggle, themeToggle);
      
      // Ambil themeIcon yang baru (setelah clone)
      themeIcon = newToggle.querySelector('#themeIcon');
      updateIcon(currentTheme);

      newToggle.addEventListener('click', () => {
        const current = htmlEl.getAttribute('data-bs-theme');
        const next = current === 'light' ? 'dark' : 'light';

        htmlEl.setAttribute('data-bs-theme', next);
        localStorage.setItem('theme', next);
        updateIcon(next);
      });
    }

    initTheme();
    document.addEventListener('livewire:navigated', initTheme);
  })();
</script>