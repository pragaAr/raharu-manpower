<?php

namespace App\Livewire\Pengajuan;

use App\Models\JadwalKaryawan;
use App\Models\Karyawan;
use App\Models\TukarShiftRequest as TukarShiftRequestModel;
use App\Models\User;
use App\Services\Pengajuan\TukarShiftRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengajuan Tukar Shift')]
class TukarShift extends Component
{
  use AuthorizesRequests, WithPagination;

  public $requestId;
  public $requesterId;
  public $targetKaryawanId;
  public $tanggal;
  public $requesterJadwalId;
  public $targetJadwalId;
  public $catatan;
  public $status = TukarShiftRequestModel::STATUS_PENDING;
  public $approvedBy;
  public $approvedAt;
  public $isEdit = false;
  public $deleteId = null;

  public $karyawans = [];
  public $requesterJadwals = [];
  public $targetJadwals = [];
  public $approvers = [];
  public $statusOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => ['except' => '', 'as' => 's'],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->statusOptions = TukarShiftRequestModel::statusList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== TukarShiftRequestModel::STATUS_APPROVED) {
      $this->approvedBy = null;
      $this->approvedAt = null;
    }
  }

  public function updatedRequesterId()
  {
    $this->requesterJadwalId = null;
    $this->refreshJadwalOptions();
  }

  public function updatedTargetKaryawanId()
  {
    $this->targetJadwalId = null;
    $this->refreshJadwalOptions();
  }

  public function updatedTanggal()
  {
    $this->requesterJadwalId = null;
    $this->targetJadwalId = null;
    $this->refreshJadwalOptions();
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = TukarShiftRequestModel::with([
      'requester',
      'targetKaryawan',
      'requesterJadwal.shift',
      'targetJadwal.shift',
      'approver'
    ])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('catatan', 'like', $like)
            ->orWhereHas('requester', function ($karyawanQuery) use ($like) {
              $karyawanQuery->where('nik', 'like', $like)
                ->orWhere('nama', 'like', $like);
            })
            ->orWhereHas('targetKaryawan', function ($karyawanQuery) use ($like) {
              $karyawanQuery->where('nik', 'like', $like)
                ->orWhere('nama', 'like', $like);
            })
            ->orWhereHas('approver', function ($approverQuery) use ($like) {
              $approverQuery->where('username', 'like', $like);
            });
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc');

    return view('livewire.pengajuan.tukar-shift', [
      'data'       => $query->paginate(10),
      'title'      => 'Pengajuan Tukar Shift',
      'hasActions' => auth()->user()->canAny([
        'pengajuan-tukar-shift.create',
        'pengajuan-tukar-shift.edit',
        'pengajuan-tukar-shift.delete',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('pengajuan-tukar-shift.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadApprovers();
    $this->refreshJadwalOptions();

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('pengajuan-tukar-shift.edit');
    $this->resetValidation();

    $request = TukarShiftRequestModel::find($id);
    if (!$request) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->requestId = $request->id;
    $this->requesterId = $request->requester_id;
    $this->targetKaryawanId = $request->target_karyawan_id;
    $this->tanggal = $request->tanggal?->format('Y-m-d');
    $this->requesterJadwalId = $request->requester_jadwal_id;
    $this->targetJadwalId = $request->target_jadwal_id;
    $this->catatan = $request->catatan;
    $this->status = $request->status;
    $this->approvedBy = $request->approved_by;
    $this->approvedAt = $request->approved_at?->format('Y-m-d\TH:i');
    $this->isEdit = true;

    $this->loadKaryawans();
    $this->loadApprovers();
    $this->refreshJadwalOptions();

    $this->dispatch('openModal');
  }

  public function save(TukarShiftRequestService $service)
  {
    $this->validate(
      [
        'requesterId'      => ['required', 'exists:karyawan,id'],
        'targetKaryawanId' => ['required', 'exists:karyawan,id', 'different:requesterId'],
        'tanggal'          => ['required', 'date'],
        'requesterJadwalId' => ['required', 'exists:jadwal_karyawan,id'],
        'targetJadwalId'   => ['required', 'exists:jadwal_karyawan,id', 'different:requesterJadwalId'],
        'catatan'          => ['nullable', 'string', 'max:255'],
        'status'           => ['required', Rule::in(TukarShiftRequestModel::statusList())],
        'approvedBy'       => [
          Rule::requiredIf($this->status === TukarShiftRequestModel::STATUS_APPROVED),
          'nullable',
          'exists:user,id',
        ],
        'approvedAt'       => [
          Rule::requiredIf($this->status === TukarShiftRequestModel::STATUS_APPROVED),
          'nullable',
          'date',
        ],
      ],
      [
        'requesterId.required' => 'Requester wajib dipilih.',
        'targetKaryawanId.required' => 'Target karyawan wajib dipilih.',
        'targetKaryawanId.different' => 'Target karyawan tidak boleh sama dengan requester.',
        'tanggal.required' => 'Tanggal wajib diisi.',
        'requesterJadwalId.required' => 'Jadwal requester wajib dipilih.',
        'targetJadwalId.required' => 'Jadwal target wajib dipilih.',
        'targetJadwalId.different' => 'Jadwal target tidak boleh sama dengan jadwal requester.',
        'approvedBy.required' => 'Approver wajib dipilih saat status approved.',
        'approvedAt.required' => 'Waktu approval wajib diisi saat status approved.',
      ]
    );

    $this->isEdit ? $this->updateData($service) : $this->storeData($service);
  }

  public function storeData(TukarShiftRequestService $service)
  {
    $this->authorize('pengajuan-tukar-shift.create');

    try {
      $service->add($this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan tukar shift berhasil ditambah.'
      ]);

      $this->resetForm();
      $this->dispatch('closeModal');
    } catch (\Throwable $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function updateData(TukarShiftRequestService $service)
  {
    $this->authorize('pengajuan-tukar-shift.edit');

    try {
      $service->update((int) $this->requestId, $this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan tukar shift berhasil diupdate.'
      ]);

      $this->resetForm();
      $this->dispatch('closeModal');
    } catch (\Throwable $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('pengajuan-tukar-shift.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData(TukarShiftRequestService $service)
  {
    $this->authorize('pengajuan-tukar-shift.delete');

    if (!$this->deleteId) {
      return;
    }

    try {
      $service->delete((int) $this->deleteId);
      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan tukar shift berhasil dihapus.'
      ]);
      $this->deleteId = null;
      $this->dispatch('closeConfirmModal');
    } catch (\Throwable $e) {
      $this->dispatch('closeConfirmModal');
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Terjadi kesalahan saat menghapus data.'
      ]);
    }
  }

  protected function loadKaryawans(): void
  {
    if (!empty($this->karyawans)) {
      return;
    }

    $this->karyawans = Karyawan::onlyAktif()
      ->select('id', 'nik', 'nama')
      ->orderBy('nama')
      ->get();
  }

  protected function loadApprovers(): void
  {
    if (!empty($this->approvers)) {
      return;
    }

    $this->approvers = User::with('karyawan:id,nama')
      ->select('id', 'username', 'karyawan_id')
      ->orderBy('username')
      ->get();
  }

  protected function refreshJadwalOptions(): void
  {
    if (!$this->tanggal) {
      $this->requesterJadwals = [];
      $this->targetJadwals = [];
      return;
    }

    $this->requesterJadwals = $this->requesterId
      ? JadwalKaryawan::with('shift:id,nama')
      ->where('karyawan_id', $this->requesterId)
      ->whereDate('tanggal', $this->tanggal)
      ->orderBy('id')
      ->get()
      : [];

    $this->targetJadwals = $this->targetKaryawanId
      ? JadwalKaryawan::with('shift:id,nama')
      ->where('karyawan_id', $this->targetKaryawanId)
      ->whereDate('tanggal', $this->tanggal)
      ->orderBy('id')
      ->get()
      : [];
  }

  protected function payload(): array
  {
    return [
      'requester_id'        => $this->requesterId,
      'target_karyawan_id'  => $this->targetKaryawanId,
      'tanggal'             => $this->tanggal,
      'requester_jadwal_id' => $this->requesterJadwalId,
      'target_jadwal_id'    => $this->targetJadwalId,
      'catatan'             => $this->catatan,
      'status'              => $this->status,
      'approved_by'         => $this->approvedBy,
      'approved_at'         => $this->approvedAt,
    ];
  }

  public function resetForm()
  {
    $this->reset([
      'requestId',
      'requesterId',
      'targetKaryawanId',
      'tanggal',
      'requesterJadwalId',
      'targetJadwalId',
      'catatan',
      'approvedBy',
      'approvedAt',
    ]);

    $this->status = TukarShiftRequestModel::STATUS_PENDING;
    $this->requesterJadwals = [];
    $this->targetJadwals = [];
  }
}
