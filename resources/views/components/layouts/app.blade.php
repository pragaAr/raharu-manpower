<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Raharu - {{ $title ?? 'Manpower' }}</title>

  <!-- Tabler CSS  -->
  <link href="{{ asset('css/vendor/tabler.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/vendor/tabler-vendors.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('js/vendor/tom-select.complete.min.js') }}"></script>
  <script src="{{ asset('js/vendor/tabler.min.js') }}" data-navigate-once></script>
  <script src="{{ asset('js/vendor/toastify.js') }}" data-navigate-once></script>
  <script src="{{ asset('js/util.js') }}" data-navigate-once></script>

  <script data-navigate-once>
    document.addEventListener('livewire:init', () => {
      const showToast = (type, message) => {
        let backgroundColor;
        if (type === 'success') {
          backgroundColor = "linear-gradient(to right, #00b09b, #96c93d)";
        } else if (type === 'error') {
          backgroundColor = "linear-gradient(to right, #ff5f6d, #ffc371)";
        } else if (type === 'warning') {
          backgroundColor = "linear-gradient(to right, #f7971e, #ffd200)";
        } else if (type === 'info') {
          backgroundColor = "linear-gradient(to right, #2193b0, #6dd5ed)";
        } else {
          backgroundColor = "#333";
        }

        Toastify({
          text: message,
          duration: 5000,
          close: true,
          gravity: "top",
          position: "left",
          stopOnFocus: true,
          style: {
            background: backgroundColor,
            borderRadius: "4px",
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            gap: "10px",
          }
        }).showToast();
      }

      Livewire.on('alert', (param) => {
        showToast(param[0].type, param[0].message);
      });

      Livewire.on('refresh-notification', () => {
      fetchNotifications();
    });
    });

    document.addEventListener('livewire:navigated', () => {
      fetchNotifications();
    });

    async function fetchNotifications() {
      const url = "/notification";
      try {
        const response = await fetch(url);
        const data = await response.json();

        const badge = document.getElementById('badgeNotification');
        const row = document.getElementById('rowNotification');

        if (!badge || !row) return;

        row.innerHTML = '';

        if (data.kontrak_akan_habis.length > 0) {
          badge.classList.remove('d-none');
          badge.classList.add('bg-warning');

          const formatDate = (dateString) => {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
          };

          data.kontrak_akan_habis.forEach(item => {
            row.innerHTML += `
              <div class="row align-items-center mb-2">
                <div class="col-auto">
                  <span class="status-dot status-dot-animated bg-warning"></span>
                </div>
                <div class="col" style="border-bottom: var(--tblr-list-group-border-width) solid var(--tblr-list-group-border-color);">
                  <p class="text-body d-block mb-1">${item.label}</p>
                  <div class="d-block text-secondary">
                    <p class="mb-0">Kontrak ke ${item.kontrak_ke}</p>
                    <p class="mb-0 text-uppercase">${item.nama} - (${item.nik})</p>
                    <p class="mb-1">
                      ${item.text} pada <strong>${formatDate(item.tgl_selesai)}</strong>
                    </p>
                  </div>
                </div>
              </div>
            `;
          });
        } else {
          badge.classList.add('d-none');
          badge.classList.remove('bg-warning');
          row.innerHTML = `<p class="text-secondary mb-0">Belum ada notifikasi</p>`;
        }

      } catch (e) {
        console.error(e);
      }
    }
  </script>

  <link rel="stylesheet" href="{{ asset('css/vendor/toastify.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/util.css') }}">

  @livewireStyles
  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>
  <script>
    // Immediate theme apply to prevent flash
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
  </script>
  @stack('styles')
</head>

<body>

  <div class="page">

    @include('partials.navbar')

    <div class="page-wrapper">
      <div class="container-xl">
        {{ $slot }}
      </div>
    </div>

    @include('partials.footer')
  </div>

  @livewireScripts

  @stack('scripts')

</body>

</html>