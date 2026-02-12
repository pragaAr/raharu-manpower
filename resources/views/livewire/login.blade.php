<div>

  <div class="card card-md mx-2 custom-card">
    <div class="card-body">
      <h2 class="h3 text-center mb-4">Silahkan login</h2>
      <form wire:submit.prevent="login" autocomplete="off" novalidate>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username anda" autocomplete="off" autofocus>
          @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">
            Password
          </label>
          <div class="input-group input-group-flat has-invalid">
            <input type="password" wire:model="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" placeholder="Password anda" autocomplete="off">
            <span class="input-group-text">
              <a href="javascript:void(0)" class="link-secondary text-decoration-none" id="togglePassword">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" id="eyeIcon">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
                  <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" />
                  <path d="M3 3l18 18" />
                </svg>
              </a>
            </span>
          </div>
          @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="form-footer">
          <button 
            type="submit" 
            class="btn btn-primary w-100"
            wire:loading.attr="disabled"
            wire:target="login">
              <span wire:loading wire:target="login" class="spinner-border spinner-border-sm me-1"></span>
            Login
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="text-center text-secondary mt-3">
    Made with 💖
    <a href="https://pragaar.github.io/raharu-landing-page" tabindex="-1">Raharu</a> {{ date('Y') }}
  </div>

  <script>
    document.getElementById('togglePassword')?.addEventListener('click', function() {
      const passwordInput = document.getElementById('passwordInput');
      const eyeIcon = document.getElementById('eyeIcon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />`;
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" />`;
      }
    });
  </script>
</div>
