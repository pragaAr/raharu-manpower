<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Spatie handles permissions automatically

    // Grant all permissions to 'Superuser' role
    \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
      if ($user->hasRole('Superuser') || $user->hasRole('Administrator')) {
        return true;
      }

      $parts = explode('.', $ability);
      if (count($parts) > 1) {
        $module = $parts[0];
        $action = $parts[1];

        // Any CUD grants View/Read
        if ($module !== 'master' && ($action === 'view' || $action === 'read')) {
          // If checking for view, grant if user has any permissions in this module
          if ($user->getAllPermissions()->contains(fn($p) => str_starts_with($p->name, $module . '.'))) {
            return true;
          }
        }
      }

      return null;
    });
  }
}
