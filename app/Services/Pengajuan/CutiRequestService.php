<?php

namespace App\Services\Pengajuan;

use App\Models\CutiRequest;
use Illuminate\Support\Facades\DB;

class CutiRequestService extends BasePengajuanService
{
  public function add(array $data, ?int $actorId = null): CutiRequest
  {
    return DB::transaction(function () use ($data, $actorId) {
      $actorId = $this->actorId($actorId);
      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateDateRange($payload['tanggal_mulai'], $payload['tanggal_selesai']);
      $this->ensureNoOverlap(
        (int) $payload['karyawan_id'],
        (string) $payload['tanggal_mulai'],
        (string) $payload['tanggal_selesai'],
      );

      $request = CutiRequest::create([
        'karyawan_id'     => $payload['karyawan_id'],
        'tanggal_mulai'   => $payload['tanggal_mulai'],
        'tanggal_selesai' => $payload['tanggal_selesai'],
        'alasan'          => $payload['alasan'] ?? null,
        'status'          => $payload['status'],
        'approved_by'     => $payload['approved_by'],
        'approved_at'     => $payload['approved_at'],
      ]);

      $this->logAction('cuti', $request->id, 'created', $actorId, $payload['alasan'] ?? null);

      return $request;
    });
  }

  public function update(int $id, array $data, ?int $actorId = null): CutiRequest
  {
    return DB::transaction(function () use ($id, $data, $actorId) {
      $actorId = $this->actorId($actorId);
      $request = CutiRequest::findOrFail($id);

      $beforeStatus = $request->status;
      $payload = $this->normalizeApproval($data, $actorId);

      $this->validateDateRange($payload['tanggal_mulai'], $payload['tanggal_selesai']);
      $this->ensureNoOverlap(
        (int) $payload['karyawan_id'],
        (string) $payload['tanggal_mulai'],
        (string) $payload['tanggal_selesai'],
        $request->id,
      );

      $request->update([
        'karyawan_id'     => $payload['karyawan_id'],
        'tanggal_mulai'   => $payload['tanggal_mulai'],
        'tanggal_selesai' => $payload['tanggal_selesai'],
        'alasan'          => $payload['alasan'] ?? null,
        'status'          => $payload['status'],
        'approved_by'     => $payload['approved_by'],
        'approved_at'     => $payload['approved_at'],
      ]);

      $this->logAction(
        'cuti',
        $request->id,
        $this->resolveAction($beforeStatus, $payload['status']),
        $actorId,
        $payload['alasan'] ?? null
      );

      return $request->refresh();
    });
  }

  public function delete(int $id): void
  {
    CutiRequest::findOrFail($id)->delete();
  }

  private function validateDateRange(string $tanggalMulai, string $tanggalSelesai): void
  {
    if ($tanggalSelesai < $tanggalMulai) {
      throw new \InvalidArgumentException('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.');
    }
  }

  private function ensureNoOverlap(
    int $karyawanId,
    string $tanggalMulai,
    string $tanggalSelesai,
    ?int $ignoreId = null
  ): void {
    $exists = CutiRequest::query()
      ->where('karyawan_id', $karyawanId)
      ->whereIn('status', [CutiRequest::STATUS_PENDING, CutiRequest::STATUS_APPROVED])
      ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
      ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
        $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
          ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
          ->orWhere(function ($sub) use ($tanggalMulai, $tanggalSelesai) {
            $sub->where('tanggal_mulai', '<=', $tanggalMulai)
              ->where('tanggal_selesai', '>=', $tanggalSelesai);
          });
      })
      ->exists();

    if ($exists) {
      throw new \InvalidArgumentException('Karyawan sudah memiliki pengajuan cuti pada rentang tanggal tersebut.');
    }
  }
}
