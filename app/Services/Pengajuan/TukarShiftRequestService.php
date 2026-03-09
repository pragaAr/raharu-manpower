<?php

namespace App\Services\Pengajuan;

use App\Models\JadwalKaryawan;
use App\Models\TukarShiftRequest;
use Illuminate\Support\Facades\DB;

class TukarShiftRequestService extends BasePengajuanService
{
  public function add(array $data, ?int $actorId = null): TukarShiftRequest
  {
    return DB::transaction(function () use ($data, $actorId) {
      $actorId = $this->actorId($actorId);
      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateKaryawanPair((int) $payload['requester_id'], (int) $payload['target_karyawan_id']);
      $this->ensureJadwalConsistency($payload);
      $this->ensureNoDuplicate($payload);

      $request = TukarShiftRequest::create([
        'requester_id'       => $payload['requester_id'],
        'target_karyawan_id' => $payload['target_karyawan_id'],
        'tanggal'            => $payload['tanggal'],
        'requester_jadwal_id' => $payload['requester_jadwal_id'],
        'target_jadwal_id'   => $payload['target_jadwal_id'],
        'catatan'            => $payload['catatan'] ?? null,
        'status'             => $payload['status'],
        'approved_by'        => $payload['approved_by'],
        'approved_at'        => $payload['approved_at'],
      ]);

      $this->logAction('tukar_shift', $request->id, 'created', $actorId, $payload['catatan'] ?? null);

      return $request;
    });
  }

  public function update(int $id, array $data, ?int $actorId = null): TukarShiftRequest
  {
    return DB::transaction(function () use ($id, $data, $actorId) {
      $actorId = $this->actorId($actorId);
      $request = TukarShiftRequest::findOrFail($id);
      $beforeStatus = $request->status;

      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateKaryawanPair((int) $payload['requester_id'], (int) $payload['target_karyawan_id']);
      $this->ensureJadwalConsistency($payload);
      $this->ensureNoDuplicate($payload, $request->id);

      $request->update([
        'requester_id'        => $payload['requester_id'],
        'target_karyawan_id'  => $payload['target_karyawan_id'],
        'tanggal'             => $payload['tanggal'],
        'requester_jadwal_id' => $payload['requester_jadwal_id'],
        'target_jadwal_id'    => $payload['target_jadwal_id'],
        'catatan'             => $payload['catatan'] ?? null,
        'status'              => $payload['status'],
        'approved_by'         => $payload['approved_by'],
        'approved_at'         => $payload['approved_at'],
      ]);

      $this->logAction(
        'tukar_shift',
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
    TukarShiftRequest::findOrFail($id)->delete();
  }

  private function validateKaryawanPair(int $requesterId, int $targetId): void
  {
    if ($requesterId === $targetId) {
      throw new \InvalidArgumentException('Requester dan target karyawan tidak boleh sama.');
    }
  }

  private function ensureJadwalConsistency(array $payload): void
  {
    $requesterJadwal = JadwalKaryawan::findOrFail($payload['requester_jadwal_id']);
    $targetJadwal = JadwalKaryawan::findOrFail($payload['target_jadwal_id']);

    if ($requesterJadwal->karyawan_id !== (int) $payload['requester_id']) {
      throw new \InvalidArgumentException('Jadwal requester tidak sesuai dengan karyawan yang dipilih.');
    }

    if ($targetJadwal->karyawan_id !== (int) $payload['target_karyawan_id']) {
      throw new \InvalidArgumentException('Jadwal target tidak sesuai dengan karyawan yang dipilih.');
    }

    if (
      $requesterJadwal->tanggal?->format('Y-m-d') !== (string) $payload['tanggal']
      || $targetJadwal->tanggal?->format('Y-m-d') !== (string) $payload['tanggal']
    ) {
      throw new \InvalidArgumentException('Tanggal request harus sama dengan tanggal kedua jadwal yang dipilih.');
    }
  }

  private function ensureNoDuplicate(array $payload, ?int $ignoreId = null): void
  {
    $exists = TukarShiftRequest::query()
      ->where('tanggal', $payload['tanggal'])
      ->where('requester_jadwal_id', $payload['requester_jadwal_id'])
      ->where('target_jadwal_id', $payload['target_jadwal_id'])
      ->whereIn('status', [TukarShiftRequest::STATUS_PENDING, TukarShiftRequest::STATUS_APPROVED])
      ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
      ->exists();

    if ($exists) {
      throw new \InvalidArgumentException('Pengajuan tukar shift dengan jadwal tersebut sudah ada.');
    }
  }
}
