<header class="navbar navbar-expand-md d-print-none sticky-top" wire:ignore>
  <div class="container-xl">

    <!-- Toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Logo -->
    @persist('navbar-logo')
    <h1 class="navbar-brand navbar-brand-autodark d-none d-md-block pe-0 pe-md-3">
      <a href="{{ url('/home') }}" wire:navigate>
        <img src="{{ asset('img/raharu-light.png') }}"
          alt="Logo"
          class="navbar-brand-image"
          style="height:1.2rem; object-fit: contain; max-width: none;">
      </a>
    </h1>
    @endpersist

    <!-- Right Menu -->
    <div class="navbar-nav flex-row order-md-last">

      <!-- Theme toggle -->
      <button type="button" class="nav-link" id="themeMode">
        <span id="themeIcon"></span>
      </button>

      <!-- Notification -->
      <div class="nav-item dropdown me-3">
        <a href="#" id="notificationDropdown" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
          <svg xmlns="http://www.w3.org/2000/svg" 
            class="icon" 
            width="24" 
            height="24" 
            viewBox="0 0 24 24"
            stroke-width="2" 
            stroke="currentColor" 
            fill="none" 
            stroke-linecap="round" 
            stroke-linejoin="round">
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

    <!-- Main Menu -->
  <div class="collapse navbar-collapse" id="navbar-menu">
      <div class="d-flex flex-column flex-fill align-items-stretch align-items-md-center">
        <ul class="navbar-nav">

          {{-- Home --}}
          <li class="nav-item {{ request()->segment(1) == 'home' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/home') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" 
                  class="icon" 
                  width="24" 
                  height="24" 
                  viewBox="0 0 24 24" stroke-width="2" 
                  stroke="currentColor" 
                  fill="none" 
                  stroke-linecap="round" 
                  stroke-linejoin="round">
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
          @if(auth()->user()->canAny(['lokasi.view', 'divisi.view', 'unit.view', 'jabatan.view', 'kategori.view', 'shift.view', 'shift-requirement.view', 'holiday.view', 'work-rule.view', 'jadwal-kerja.view', 'jadwal-lembur.view']))
          <li class="nav-item dropdown
          {{ in_array(request()->segment(1), ['lokasi','divisi','unit','jabatan', 'kategori', 'shift', 'shift-requirement', 'holiday', 'work-rule', 'jadwal-kerja', 'jadwal-lembur']) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" 
                  width="24" 
                  height="24" 
                  viewBox="0 0 24 24" 
                  fill="none" stroke="currentColor" 
                  stroke-width="2" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  class="icon icon-tabler icons-tabler-outline icon-tabler-stack-2">
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

              {{-- Nested: Work Config --}}
              @if(auth()->user()->canAny(['shift.view', 'shift-requirement.view', 'holiday.view', 'work-rule.view']))
              <div class="dropend">
                <a class="dropdown-item dropdown-toggle {{ in_array(request()->segment(1), ['shift','shift-requirement','holiday','work-rule']) ? 'active' : '' }}" href="#sidebar-work-config" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                  - Work Config
                </a>
                <div class="dropdown-menu">
                  @can('shift.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'shift' ? 'active' : '' }}" href="{{ url('/shift') }}" wire:navigate>Shift</a>
                  @endcan
                  @can('shift-requirement.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'shift-requirement' ? 'active' : '' }}" href="{{ url('/shift-requirement') }}" wire:navigate>Shift Requirement</a>
                  @endcan
                  @can('holiday.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'holiday' ? 'active' : '' }}" href="{{ url('/holiday') }}" wire:navigate>Hari Libur</a>
                  @endcan
                  @can('work-rule.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'work-rule' ? 'active' : '' }}" href="{{ url('/work-rule') }}" wire:navigate>Aturan Kerja</a>
                  @endcan
                </div>
              </div>
              @endif

              {{-- Nested: Penjadwalan --}}
              @if(auth()->user()->canAny(['jadwal-kerja.view', 'jadwal-lembur.view']))
              <div class="dropend">
                <a class="dropdown-item dropdown-toggle {{ in_array(request()->segment(1), ['jadwal-kerja','jadwal-lembur']) ? 'active' : '' }}" href="#sidebar-penjadwalan" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                  - Penjadwalan
                </a>
                <div class="dropdown-menu">
                  @can('jadwal-kerja.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'jadwal-kerja' ? 'active' : '' }}" href="{{ url('/jadwal-kerja') }}" wire:navigate>Jadwal Kerja</a>
                  @endcan
                  @can('jadwal-lembur.view')
                  <a class="dropdown-item {{ request()->segment(1) == 'jadwal-lembur' ? 'active' : '' }}" href="{{ url('/jadwal-lembur') }}" wire:navigate>Jadwal Lembur</a>
                  @endcan
                </div>
              </div>
              @endif
            </div>
          </li>
          @endif

          {{-- Karyawan --}}
          @can('karyawan.view')
          <li class="nav-item {{ request()->segment(1) == 'karyawan' ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/karyawan') }}" wire:navigate>
              <span class="nav-link-icon d-md-none d-lg-inline-block">
               <svg xmlns="http://www.w3.org/2000/svg"
                  width="24" 
                  height="24" 
                  viewBox="0 0 24 24" 
                  fill="none" stroke="currentColor" 
                  stroke-width="2" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
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
                fill="none" stroke="currentColor" 
                stroke-width="2" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-check">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M20.942 13.021a9 9 0 1 0 -9.407 7.967" />
                  <path d="M12 7v5l3 3" />
                  <path d="M15 19l2 2l4 -4" />
                </svg>
              </span>
              <span class="nav-link-title">
                Absensi
              </span>
            </a>
          </li>
          @endcan

          {{-- Pengajuan Dropdown --}}
          @if(auth()->user()->canAny(['pengajuan-cuti.view', 'pengajuan-tukar-shift.view', 'pengajuan-lembur.view', 'pengajuan-double-shift.view']))
          <li class="nav-item dropdown
          {{ request()->segment(1) == 'pengajuan' ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none" stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 9l1 0" />
                    <path d="M9 13l6 0" />
                    <path d="M9 17l6 0" />
                </svg>
              </span>
              <span class="nav-link-title">Pengajuan</span>
            </a>
            <div class="dropdown-menu">
              @can('pengajuan-cuti.view')
              <a class="dropdown-item {{ request()->segment(2) == 'cuti' ? 'active' : '' }}" href="{{ url('/pengajuan/cuti') }}" wire:navigate>
                - Cuti
              </a>
              @endcan
              @can('pengajuan-tukar-shift.view')
              <a class="dropdown-item {{ request()->segment(2) == 'tukar-shift' ? 'active' : '' }}" href="{{ url('/pengajuan/tukar-shift') }}" wire:navigate>
                - Tukar Shift
              </a>
              @endcan
              @can('pengajuan-lembur.view')
              <a class="dropdown-item {{ request()->segment(2) == 'perubahan-lembur' ? 'active' : '' }}" href="{{ url('/pengajuan/perubahan-lembur') }}" wire:navigate>
                - Perubahan Lembur
              </a>
              @endcan
              @can('pengajuan-double-shift.view')
              <a class="dropdown-item {{ request()->segment(2) == 'double-shift' ? 'active' : '' }}" href="{{ url('/pengajuan/double-shift') }}" wire:navigate>
                - Double Shift
              </a>
              @endcan
            </div>
          </li>
          @endif

          {{-- Permission Dropdown --}}
          @if(auth()->user()->canAny(['role.view', 'permission.view', 'user.view']))
          <li class="nav-item dropdown
          {{ in_array(request()->segment(1), ['role', 'permission', 'user']) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" 
                  width="24" 
                  height="24" 
                  viewBox="0 0 24 24" 
                  fill="none" stroke="currentColor" 
                  stroke-width="2" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  class="icon icon-tabler icons-tabler-outline icon-tabler-shield-lock">
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
              @can('user.view')
              <a class="dropdown-item {{ request()->segment(1) == 'user' ? 'active' : '' }}" href="{{ url('/user') }}" wire:navigate>
                - User
              </a>
              @endcan
            </div>
          </li>
          @endif

        </ul>
      </div>
    </div>

  </div>
</header>

<script>
  (function() {
    var htmlEl, themeToggle, themeIcon;

    const sunIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" 
        class="icon" 
        width="24" 
        height="24"
        viewBox="0 0 24 24" 
        stroke-width="2" 
        stroke="currentColor" 
        fill="none"
        stroke-linecap="round" 
        stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
          <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l .7 .7m12.1 -.7l -.7 .7m0 11.4l .7 .7m-12.1 -.7l -.7 .7" />
      </svg>
    `;

    const moonIcon = `
      <svg xmlns="http://www.w3.org/2000/svg" 
        class="icon" 
        width="24" 
        height="24"
        viewBox="0 0 24 24" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="2"
        stroke-linecap="round" 
        stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg>
    `;

    function updateIcon(theme) {
      if (themeIcon) {
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
