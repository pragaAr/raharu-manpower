<?php

namespace App\Services\Log;

use Illuminate\Support\Facades\Log;

class AbsensiLogger
{
  public static function info(string $message, array $context = []): void
  {
    Log::channel('absensi')->info($message, $context);
  }

  public static function error(string $message, array $context = []): void
  {
    Log::channel('absensi')->error($message, $context);
  }
}
