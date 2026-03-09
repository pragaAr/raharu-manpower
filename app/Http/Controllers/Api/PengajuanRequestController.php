<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\DoubleShiftRequest;
use App\Models\PerubahanLemburRequest;
use App\Models\TukarShiftRequest;
use App\Services\Pengajuan\CutiRequestService;
use App\Services\Pengajuan\DoubleShiftRequestService;
use App\Services\Pengajuan\PerubahanLemburRequestService;
use App\Services\Pengajuan\TukarShiftRequestService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengajuanRequestController extends Controller
{
  public function cutiIndex(Request $request)
  {
    $this->authorizePermission($request, 'pengajuan-cuti.view');

    $request->validate([
      'search'    => ['nullable', 'string'],
      'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $term = trim((string) $request->input('search', ''));
    $perPage = (int) $request->integer('per_page', 15);

    $records = CutiRequest::with(['karyawan:id,nik,nama', 'approver:id,username'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";
        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal_mulai', 'like', $like)
            ->orWhere('tanggal_selesai', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('alasan', 'like', $like)
            ->orWhereHas('karyawan', fn($k) => $k->where('nik', 'like', $like)->orWhere('nama', 'like', $like));
        });
      })
      ->orderBy('tanggal_mulai', 'desc')
      ->orderBy('id', 'desc')
      ->paginate($perPage);

    return response()->json($this->paginatePayload($records, 'Data pengajuan cuti.'));
  }

  public function cutiStore(Request $request, CutiRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-cuti.create');

    $payload = $request->validate([
      'karyawan_id'     => ['required', 'exists:karyawan,id'],
      'tanggal_mulai'   => ['required', 'date'],
      'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
      'alasan'          => ['nullable', 'string', 'max:255'],
      'status'          => ['nullable', Rule::in(CutiRequest::statusList())],
      'approved_by'     => ['nullable', 'exists:user,id'],
      'approved_at'     => ['nullable', 'date'],
    ]);

    $payload['status'] = $payload['status'] ?? CutiRequest::STATUS_PENDING;

    try {
      $data = $service->add($payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan cuti berhasil dibuat.',
        'data'    => $data->load(['karyawan:id,nik,nama', 'approver:id,username']),
      ], 201);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function cutiUpdate(Request $request, int $id, CutiRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-cuti.edit');

    $payload = $request->validate([
      'karyawan_id'     => ['required', 'exists:karyawan,id'],
      'tanggal_mulai'   => ['required', 'date'],
      'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
      'alasan'          => ['nullable', 'string', 'max:255'],
      'status'          => ['required', Rule::in(CutiRequest::statusList())],
      'approved_by'     => ['nullable', 'exists:user,id'],
      'approved_at'     => ['nullable', 'date'],
    ]);

    try {
      $data = $service->update($id, $payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan cuti berhasil diupdate.',
        'data'    => $data->load(['karyawan:id,nik,nama', 'approver:id,username']),
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function cutiDelete(Request $request, int $id, CutiRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-cuti.delete');

    try {
      $service->delete($id);

      return response()->json(['message' => 'Pengajuan cuti berhasil dihapus.']);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function tukarShiftIndex(Request $request)
  {
    $this->authorizePermission($request, 'pengajuan-tukar-shift.view');

    $request->validate([
      'search'    => ['nullable', 'string'],
      'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $term = trim((string) $request->input('search', ''));
    $perPage = (int) $request->integer('per_page', 15);

    $records = TukarShiftRequest::with([
      'requester:id,nik,nama',
      'targetKaryawan:id,nik,nama',
      'requesterJadwal:id,shift_nama,jam_masuk,jam_pulang',
      'targetJadwal:id,shift_nama,jam_masuk,jam_pulang',
      'approver:id,username',
    ])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";
        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('catatan', 'like', $like)
            ->orWhereHas('requester', fn($k) => $k->where('nik', 'like', $like)->orWhere('nama', 'like', $like))
            ->orWhereHas('targetKaryawan', fn($k) => $k->where('nik', 'like', $like)->orWhere('nama', 'like', $like));
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc')
      ->paginate($perPage);

    return response()->json($this->paginatePayload($records, 'Data pengajuan tukar shift.'));
  }

  public function tukarShiftStore(Request $request, TukarShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-tukar-shift.create');

    $payload = $request->validate([
      'requester_id'       => ['required', 'exists:karyawan,id'],
      'target_karyawan_id' => ['required', 'exists:karyawan,id', 'different:requester_id'],
      'tanggal'            => ['required', 'date'],
      'requester_jadwal_id' => ['required', 'exists:jadwal_karyawan,id'],
      'target_jadwal_id'   => ['required', 'exists:jadwal_karyawan,id', 'different:requester_jadwal_id'],
      'catatan'            => ['nullable', 'string', 'max:255'],
      'status'             => ['nullable', Rule::in(TukarShiftRequest::statusList())],
      'approved_by'        => ['nullable', 'exists:user,id'],
      'approved_at'        => ['nullable', 'date'],
    ]);

    $payload['status'] = $payload['status'] ?? TukarShiftRequest::STATUS_PENDING;

    try {
      $data = $service->add($payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan tukar shift berhasil dibuat.',
        'data'    => $data->load([
          'requester:id,nik,nama',
          'targetKaryawan:id,nik,nama',
          'requesterJadwal:id,shift_nama,jam_masuk,jam_pulang',
          'targetJadwal:id,shift_nama,jam_masuk,jam_pulang',
          'approver:id,username',
        ]),
      ], 201);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function tukarShiftUpdate(Request $request, int $id, TukarShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-tukar-shift.edit');

    $payload = $request->validate([
      'requester_id'       => ['required', 'exists:karyawan,id'],
      'target_karyawan_id' => ['required', 'exists:karyawan,id', 'different:requester_id'],
      'tanggal'            => ['required', 'date'],
      'requester_jadwal_id' => ['required', 'exists:jadwal_karyawan,id'],
      'target_jadwal_id'   => ['required', 'exists:jadwal_karyawan,id', 'different:requester_jadwal_id'],
      'catatan'            => ['nullable', 'string', 'max:255'],
      'status'             => ['required', Rule::in(TukarShiftRequest::statusList())],
      'approved_by'        => ['nullable', 'exists:user,id'],
      'approved_at'        => ['nullable', 'date'],
    ]);

    try {
      $data = $service->update($id, $payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan tukar shift berhasil diupdate.',
        'data'    => $data->load([
          'requester:id,nik,nama',
          'targetKaryawan:id,nik,nama',
          'requesterJadwal:id,shift_nama,jam_masuk,jam_pulang',
          'targetJadwal:id,shift_nama,jam_masuk,jam_pulang',
          'approver:id,username',
        ]),
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function tukarShiftDelete(Request $request, int $id, TukarShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-tukar-shift.delete');

    try {
      $service->delete($id);

      return response()->json(['message' => 'Pengajuan tukar shift berhasil dihapus.']);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function perubahanLemburIndex(Request $request)
  {
    $this->authorizePermission($request, 'pengajuan-lembur.view');

    $request->validate([
      'search'    => ['nullable', 'string'],
      'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $term = trim((string) $request->input('search', ''));
    $perPage = (int) $request->integer('per_page', 15);

    $records = PerubahanLemburRequest::with([
      'karyawan:id,nik,nama',
      'jadwalLembur:id,tanggal,jam_mulai,jam_selesai',
      'approver:id,username'
    ])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";
        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('alasan', 'like', $like)
            ->orWhereHas('karyawan', fn($k) => $k->where('nik', 'like', $like)->orWhere('nama', 'like', $like));
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc')
      ->paginate($perPage);

    return response()->json($this->paginatePayload($records, 'Data pengajuan perubahan lembur.'));
  }

  public function perubahanLemburStore(Request $request, PerubahanLemburRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-lembur.create');

    $payload = $request->validate([
      'karyawan_id'      => ['required', 'exists:karyawan,id'],
      'jadwal_lembur_id' => ['required', 'exists:jadwal_lembur,id'],
      'jam_mulai_baru'   => ['required', 'date_format:H:i'],
      'jam_selesai_baru' => ['required', 'date_format:H:i', 'after:jam_mulai_baru'],
      'alasan'           => ['nullable', 'string', 'max:255'],
      'status'           => ['nullable', Rule::in(PerubahanLemburRequest::statusList())],
      'approved_by'      => ['nullable', 'exists:user,id'],
      'approved_at'      => ['nullable', 'date'],
    ]);

    $payload['status'] = $payload['status'] ?? PerubahanLemburRequest::STATUS_PENDING;

    try {
      $data = $service->add($payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan perubahan lembur berhasil dibuat.',
        'data'    => $data->load([
          'karyawan:id,nik,nama',
          'jadwalLembur:id,tanggal,jam_mulai,jam_selesai',
          'approver:id,username'
        ]),
      ], 201);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function perubahanLemburUpdate(Request $request, int $id, PerubahanLemburRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-lembur.edit');

    $payload = $request->validate([
      'karyawan_id'      => ['required', 'exists:karyawan,id'],
      'jadwal_lembur_id' => ['required', 'exists:jadwal_lembur,id'],
      'jam_mulai_baru'   => ['required', 'date_format:H:i'],
      'jam_selesai_baru' => ['required', 'date_format:H:i', 'after:jam_mulai_baru'],
      'alasan'           => ['nullable', 'string', 'max:255'],
      'status'           => ['required', Rule::in(PerubahanLemburRequest::statusList())],
      'approved_by'      => ['nullable', 'exists:user,id'],
      'approved_at'      => ['nullable', 'date'],
    ]);

    try {
      $data = $service->update($id, $payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan perubahan lembur berhasil diupdate.',
        'data'    => $data->load([
          'karyawan:id,nik,nama',
          'jadwalLembur:id,tanggal,jam_mulai,jam_selesai',
          'approver:id,username'
        ]),
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function perubahanLemburDelete(Request $request, int $id, PerubahanLemburRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-lembur.delete');

    try {
      $service->delete($id);

      return response()->json(['message' => 'Pengajuan perubahan lembur berhasil dihapus.']);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function doubleShiftIndex(Request $request)
  {
    $this->authorizePermission($request, 'pengajuan-double-shift.view');

    $request->validate([
      'search'    => ['nullable', 'string'],
      'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
    ]);

    $term = trim((string) $request->input('search', ''));
    $perPage = (int) $request->integer('per_page', 15);

    $records = DoubleShiftRequest::with([
      'karyawan:id,nik,nama',
      'shiftAwal:id,nama,jam_masuk,jam_pulang',
      'shiftTambahan:id,nama,jam_masuk,jam_pulang',
      'approver:id,username',
    ])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";
        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('catatan', 'like', $like)
            ->orWhereHas('karyawan', fn($k) => $k->where('nik', 'like', $like)->orWhere('nama', 'like', $like));
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc')
      ->paginate($perPage);

    return response()->json($this->paginatePayload($records, 'Data pengajuan double shift.'));
  }

  public function doubleShiftStore(Request $request, DoubleShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-double-shift.create');

    $payload = $request->validate([
      'karyawan_id'       => ['required', 'exists:karyawan,id'],
      'tanggal'           => ['required', 'date'],
      'shift_awal_id'     => ['required', 'exists:shift_master,id'],
      'shift_tambahan_id' => ['required', 'exists:shift_master,id', 'different:shift_awal_id'],
      'catatan'           => ['nullable', 'string', 'max:255'],
      'status'            => ['nullable', Rule::in(DoubleShiftRequest::statusList())],
      'approved_by'       => ['nullable', 'exists:user,id'],
      'approved_at'       => ['nullable', 'date'],
    ]);

    $payload['status'] = $payload['status'] ?? DoubleShiftRequest::STATUS_PENDING;

    try {
      $data = $service->add($payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan double shift berhasil dibuat.',
        'data'    => $data->load([
          'karyawan:id,nik,nama',
          'shiftAwal:id,nama,jam_masuk,jam_pulang',
          'shiftTambahan:id,nama,jam_masuk,jam_pulang',
          'approver:id,username',
        ]),
      ], 201);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function doubleShiftUpdate(Request $request, int $id, DoubleShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-double-shift.edit');

    $payload = $request->validate([
      'karyawan_id'       => ['required', 'exists:karyawan,id'],
      'tanggal'           => ['required', 'date'],
      'shift_awal_id'     => ['required', 'exists:shift_master,id'],
      'shift_tambahan_id' => ['required', 'exists:shift_master,id', 'different:shift_awal_id'],
      'catatan'           => ['nullable', 'string', 'max:255'],
      'status'            => ['required', Rule::in(DoubleShiftRequest::statusList())],
      'approved_by'       => ['nullable', 'exists:user,id'],
      'approved_at'       => ['nullable', 'date'],
    ]);

    try {
      $data = $service->update($id, $payload, (int) $request->user()->id);

      return response()->json([
        'message' => 'Pengajuan double shift berhasil diupdate.',
        'data'    => $data->load([
          'karyawan:id,nik,nama',
          'shiftAwal:id,nama,jam_masuk,jam_pulang',
          'shiftTambahan:id,nama,jam_masuk,jam_pulang',
          'approver:id,username',
        ]),
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function doubleShiftDelete(Request $request, int $id, DoubleShiftRequestService $service)
  {
    $this->authorizePermission($request, 'pengajuan-double-shift.delete');

    try {
      $service->delete($id);

      return response()->json(['message' => 'Pengajuan double shift berhasil dihapus.']);
    } catch (\Throwable $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  private function authorizePermission(Request $request, string $permission): void
  {
    abort_unless($request->user()?->can($permission), 403, 'Tidak memiliki akses.');
  }

  private function paginatePayload($records, string $message): array
  {
    return [
      'message' => $message,
      'data'    => $records->items(),
      'meta'    => [
        'current_page' => $records->currentPage(),
        'last_page'    => $records->lastPage(),
        'per_page'     => $records->perPage(),
        'total'        => $records->total(),
        'has_more'     => $records->hasMorePages(),
      ],
    ];
  }
}
