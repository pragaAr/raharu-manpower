<?php

namespace App\Services\Pengajuan;

use App\Models\DoubleShiftRequest;
use Illuminate\Support\Facades\DB;

class DoubleShiftRequestService extends BasePengajuanService
{
  public function add(array $data, ?int $actorId = null): DoubleShiftRequest
  {
    return DB::transaction(function () use ($data, $actorId) {
      $actorId = $this->actorId($actorId);
      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateShiftPair((int) $payload['shift_awal_id'], (int) $payload['shift_tambahan_id']);
      $this->ensureNoDuplicate($payload);

      $request = DoubleShiftRequest::create([
        'karyawan_id'      => $payload['karyawan_id'],
        'tanggal'          => $payload['tanggal'],
        'shift_awal_id'    => $payload['shift_awal_id'],
        'shift_tambahan_id' => $payload['shift_tambahan_id'],
        'catatan'          => $payload['catatan'] ?? null,
        'status'           => $payload['status'],
        'approved_by'      => $payload['approved_by'],
        'approved_at'      => $payload['approved_at'],
      ]);

      $this->logAction('double_shift', $request->id, 'created', $actorId, $payload['catatan'] ?? null);

      return $request;
    });
  }

  public function update(int $id, array $data, ?int $actorId = null): DoubleShiftRequest
  {
    return DB::transaction(function () use ($id, $data, $actorId) {
      $actorId = $this->actorId($actorId);
      $request = DoubleShiftRequest::findOrFail($id);
      $beforeStatus = $request->status;

      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateShiftPair((int) $payload['shift_awal_id'], (int) $payload['shift_tambahan_id']);
      $this->ensureNoDuplicate($payload, $request->id);

      $request->update([
        'karyawan_id'       => $payload['karyawan_id'],
        'tanggal'           => $payload['tanggal'],
        'shift_awal_id'     => $payload['shift_awal_id'],
        'shift_tambahan_id' => $payload['shift_tambahan_id'],
        'catatan'           => $payload['catatan'] ?? null,
        'status'            => $payload['status'],
        'approved_by'       => $payload['approved_by'],
        'approved_at'       => $payload['approved_at'],
      ]);

      $this->logAction(
        'double_shift',
        $request->id,
        $this->resolveAction($beforeStatus, $payload['status']),
        $actorId,
        $payload['catatan'] ?? null
      );

      return $request->refresh();
    });
  }

  public function delete(int $id): void
  {
    DoubleShiftRequest::findOrFail($id)->delete();
  }

  private function validateShiftPair(int $shiftAwalId, int $shiftTambahanId): void
  {
    if ($shiftAwalId === $shiftTambahanId) {
      throw new \InvalidArgumentException('Shift awal dan shift tambahan tidak boleh sama.');
    }
  }

  private function ensureNoDuplicate(array $payload, ?int $ignoreId = null): void
  {
    $exists = DoubleShiftRequest::query()
      ->where('karyawan_id', $payload['karyawan_id'])
      ->where('tanggal', $payload['tanggal'])
      ->where('shift_awal_id', $payload['shift_awal_id'])
      ->where('shift_tambahan_id', $payload['shift_tambahan_id'])
      ->whereIn('status', [
        DoubleShiftRequest::STATUS_PENDING,
        DoubleShiftRequest::STATUS_APPROVED
      ])
      ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
      ->exists();

    if ($exists) {
      throw new \InvalidArgumentException('Pengajuan double shift yang sama sudah ada.');
    }
  }
}
