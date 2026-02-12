<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Raharu - {{ $title ?? 'Manpower' }}</title>

  <!-- Tabler CSS  -->
  <link href="{{ asset('css/vendor/tabler.min.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/vendor/toastify.min.css') }}">

  <style>
    .custom-card {
      --offset: 2px;
      position: relative;
      overflow: hidden;
      border-radius: 5px;
    }
  
    .custom-card::before {
      content: '';
      background: conic-gradient(transparent 315deg,
          rgba(0, 83, 166, 0.5),
          rgba(255, 0, 0, 0.5),
          rgba(255, 255, 255, 0.5));
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      aspect-ratio: 1;
      width: 110%;
      animation: rotate 15s linear infinite;
    }
  
    .custom-card::after {
      content: '';
      background: inherit;
      border-radius: inherit;
      position: absolute;
      inset: var(--offset);
      height: calc(100% - 2 * var(--offset));
      width: calc(100% - 2 * var(--offset));
    }
  
    .custom-card .card-body {
      z-index: 2;
    }
  
    @keyframes rotate {
      from {
        transform: translate(-50%, -50%) scale(1.4) rotate(0turn);
      }
  
      to {
        transform: translate(-50%, -50%) scale(1.4) rotate(1turn);
      }
    }
  
    .icon-alert {
      width: 2rem;
      height: 2rem;
    }
  
    .alert-dismissible .btn-close {
      top: auto;
    }

    .input-group .form-control.is-invalid ~ .input-group-text {
      border-color: #d63939 !important;
    }

    .input-group .form-control.is-invalid:focus ~ .input-group-text {
      border-color: #d63939 !important;
    }

    .input-group-flat:focus-within .form-control.is-invalid {
      border-color: #d63939 !important;
    }

    .input-group-flat .form-control.is-invalid {
      box-shadow: none;
    }

    .input-group-flat:has(.form-control.is-invalid:focus) {
      box-shadow: 0 0 0 0.25rem rgba(214, 57, 57, 0.25) !important;
    }

    .input-group-flat:focus-within:has(.form-control.is-invalid) {
      box-shadow: 0 0 0 0.25rem rgba(214, 57, 57, 0.25) !important;
    }
  </style>
  
  @livewireStyles
  <script>
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
  </script>
</head>

<body class="d-flex flex-column">
  <div class="page page-center">
    <div class="container container-tight py-4">

      <div class="text-center mb-3">
        <a href="." class="navbar-brand navbar-brand-autodark">
          <img src="{{ asset('img/raharu-light.png') }}" alt="Logo" class="navbar-brand-image">
        </a>
      </div>
        {{ $slot }}
    </div>
  </div>

  @livewireScripts
  <script src="{{ asset('js/vendor/toastify.js') }}"></script>
  <script>
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

      @if(session()->has('alert'))
        const sessionAlert = @json(session('alert'));
        showToast(sessionAlert.type, sessionAlert.message);
      @endif
    });
  </script>
</body>

</html>
