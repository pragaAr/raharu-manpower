<?php

namespace App\Services\Log;

use Illuminate\Support\Facades\Log;

class AuditLogger
{
  public static function info(string $message, array $context = []): void
  {
    Log::channel('audit')->info($message, $context);
  }

  public static function error(string $message, array $context = []): void
  {
    Log::channel('audit')->error($message, $context);
  }
}
