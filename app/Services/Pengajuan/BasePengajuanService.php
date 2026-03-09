<?php

namespace App\Services\Pengajuan;

use App\Models\RequestLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class BasePengajuanService
{
  protected function actorId(?int $actorId = null): int
  {
    $id = $actorId ?? Auth::id();

    if (!$id) {
      throw new \RuntimeException('Aktor tidak ditemukan.');
    }

    return (int) $id;
  }

  protected function normalizeApproval(array $payload, int $defaultApproverId): array
  {
    $status = strtolower((string) ($payload['status'] ?? 'pending'));
    $payload['status'] = $status;

    if ($status === 'approved') {
      $payload['approved_by'] = $payload['approved_by'] ?? $defaultApproverId;
      $payload['approved_at'] = isset($payload['approved_at']) && filled($payload['approved_at'])
        ? Carbon::parse($payload['approved_at'])->toDateTimeString()
        : now()->toDateTimeString();

      return $payload;
    }

    $payload['approved_by'] = null;
    $payload['approved_at'] = null;

    return $payload;
  }

  protected function resolveAction(?string $beforeStatus, string $afterStatus): string
  {
    if ($beforeStatus === null) {
      return 'created';
    }

    if ($beforeStatus === $afterStatus) {
      return 'revised';
    }

    if (in_array($afterStatus, ['approved', 'rejected', 'cancelled'], true)) {
      return $afterStatus;
    }

    return 'revised';
  }

  protected function logAction(
    string $requestType,
    int $requestId,
    string $action,
    int $actorId,
    ?string $catatan = null
  ): void {
    RequestLog::create([
      'request_type' => $requestType,
      'request_id'   => $requestId,
      'action'       => $action,
      'actor_id'     => $actorId,
      'catatan'      => $catatan,
      'created_at'   => now(),
    ]);
  }
}
