<?php

namespace App\Services\Pengajuan;

use App\Models\JadwalLembur;
use App\Models\PerubahanLemburRequest;
use Illuminate\Support\Facades\DB;

class PerubahanLemburRequestService extends BasePengajuanService
{
  public function add(array $data, ?int $actorId = null): PerubahanLemburRequest
  {
    return DB::transaction(function () use ($data, $actorId) {
      $actorId = $this->actorId($actorId);
      $payload = $this->normalizeApproval($data, $actorId);
      $payload = $this->normalizeByJadwal($payload);

      $this->validateNewTimeRange($payload['jam_mulai_baru'], $payload['jam_selesai_baru']);
      $this->ensureNoDuplicate($payload);

      $request = PerubahanLemburRequest::create([
        'karyawan_id'      => $payload['karyawan_id'],
        'jadwal_lembur_id' => $payload['jadwal_lembur_id'],
        'tanggal'          => $payload['tanggal'],
        'jam_mulai_lama'   => $payload['jam_mulai_lama'],
        'jam_selesai_lama' => $payload['jam_selesai_lama'],
        'jam_mulai_baru'   => $payload['jam_mulai_baru'],
        'jam_selesai_baru' => $payload['jam_selesai_baru'],
        'alasan'           => $payload['alasan'] ?? null,
        'status'           => $payload['status'],
        'approved_by'      => $payload['approved_by'],
        'approved_at'      => $payload['approved_at'],
      ]);

      $this->logAction('perubahan_lembur', $request->id, 'created', $actorId, $payload['alasan'] ?? null);

      return $request;
    });
  }

  public function update(int $id, array $data, ?int $actorId = null): PerubahanLemburRequest
  {
    return DB::transaction(function () use ($id, $data, $actorId) {
      $actorId = $this->actorId($actorId);
      $request = PerubahanLemburRequest::findOrFail($id);
      $beforeStatus = $request->status;

      $payload = $this->normalizeApproval($data, $actorId);
      $payload = $this->normalizeByJadwal($payload);

      $this->validateNewTimeRange($payload['jam_mulai_baru'], $payload['jam_selesai_baru']);
      $this->ensureNoDuplicate($payload, $request->id);

      $request->update([
        'karyawan_id'      => $payload['karyawan_id'],
        'jadwal_lembur_id' => $payload['jadwal_lembur_id'],
        'tanggal'          => $payload['tanggal'],
        'jam_mulai_lama'   => $payload['jam_mulai_lama'],
        'jam_selesai_lama' => $payload['jam_selesai_lama'],
        'jam_mulai_baru'   => $payload['jam_mulai_baru'],
        'jam_selesai_baru' => $payload['jam_selesai_baru'],
        'alasan'           => $payload['alasan'] ?? null,
        'status'           => $payload['status'],
        'approved_by'      => $payload['approved_by'],
        'approved_at'      => $payload['approved_at'],
      ]);

      $this->logAction(
        'perubahan_lembur',
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
    PerubahanLemburRequest::findOrFail($id)->delete();
  }

  private function normalizeByJadwal(array $payload): array
  {
    $jadwal = JadwalLembur::findOrFail($payload['jadwal_lembur_id']);

    if ((int) $payload['karyawan_id'] !== (int) $jadwal->karyawan_id) {
      throw new \InvalidArgumentException('Jadwal lembur tidak sesuai dengan karyawan yang dipilih.');
    }

    $payload['tanggal'] = $jadwal->tanggal?->format('Y-m-d');
    $payload['jam_mulai_lama'] = $jadwal->jam_mulai?->format('H:i:s');
    $payload['jam_selesai_lama'] = $jadwal->jam_selesai?->format('H:i:s');

    if (!$payload['tanggal'] || !$payload['jam_mulai_lama'] || !$payload['jam_selesai_lama']) {
      throw new \InvalidArgumentException('Data jadwal lembur belum lengkap.');
    }

    return $payload;
  }

  private function validateNewTimeRange(string $jamMulaiBaru, string $jamSelesaiBaru): void
  {
    if ($jamSelesaiBaru <= $jamMulaiBaru) {
      throw new \InvalidArgumentException('Jam selesai baru harus lebih besar dari jam mulai baru.');
    }
  }

  private function ensureNoDuplicate(array $payload, ?int $ignoreId = null): void
  {
    $exists = PerubahanLemburRequest::query()
      ->where('jadwal_lembur_id', $payload['jadwal_lembur_id'])
      ->whereIn('status', [
        PerubahanLemburRequest::STATUS_PENDING,
        PerubahanLemburRequest::STATUS_APPROVED
      ])
      ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
      ->exists();

    if ($exists) {
      throw new \InvalidArgumentException('Request perubahan untuk jadwal lembur tersebut sudah ada.');
    }
  }
}
