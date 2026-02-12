<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use Livewire\Component;
use Livewire\Attributes\{
  Title,
  Layout,
  Validate
};

#[Title('Login')]
#[Layout('components.layouts.guest')]
class Login extends Component
{
  #[Validate('required', message: 'Username tidak boleh kosong')]
  public $username;

  #[Validate('required', message: 'Password tidak boleh kosong')]
  public $password;

  public function login()
  {
    $this->validate();

    $throttleKey = Str::lower($this->username) . '|' . request()->ip();

    // Rate limiting: maks 5 percobaan per 60 detik
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
      $seconds = RateLimiter::availableIn($throttleKey);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
      ]);
      return;
    }

    $credentials = [
      'username' => $this->username,
      'password' => $this->password
    ];

    if (!Auth::validate($credentials)) {
      RateLimiter::hit($throttleKey, 60);
      $this->dispatch('alert', [
        'type'    => 'warning',
        'message' => 'Username atau password salah.',
      ]);
      return;
    }

    // Cek karyawan, status, dan permission sebelum login
    $user = User::where('username', $this->username)->first();

    // Admin (tanpa karyawan) = entitas sistem, bypass cek karyawan
    // User biasa wajib punya karyawan aktif
    if (!$user->isAdministrator()) {
      if (!$user->karyawan || $user->karyawan->status !== 'aktif' || !$user->can('web.access')) {
        RateLimiter::hit($throttleKey, 60);
        $this->dispatch('alert', [
          'type'    => 'warning',
          'message' => 'Username atau password salah.',
        ]);
        return;
      }
    }

    // Login berhasil, reset rate limiter
    RateLimiter::clear($throttleKey);

    Auth::login($user);
    session()->regenerate();

    return redirect('/home');
  }

  public function render()
  {
    return view('livewire.login');
  }
}
