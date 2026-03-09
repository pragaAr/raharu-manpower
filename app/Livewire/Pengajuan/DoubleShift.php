<?php

namespace App\Livewire\Pengajuan;

use App\Models\DoubleShiftRequest as DoubleShiftRequestModel;
use App\Models\Karyawan;
use App\Models\ShiftMaster;
use App\Models\User;
use App\Services\Pengajuan\DoubleShiftRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengajuan Double Shift')]
class DoubleShift extends Component
{
  use AuthorizesRequests, WithPagination;

  public $requestId;
  public $karyawanId;
  public $tanggal;
  public $shiftAwalId;
  public $shiftTambahanId;
  public $catatan;
  public $status = DoubleShiftRequestModel::STATUS_PENDING;
  public $approvedBy;
  public $approvedAt;
  public $isEdit = false;
  public $deleteId = null;

  public $karyawans = [];
  public $shifts = [];
  public $approvers = [];
  public $statusOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => ['except' => '', 'as' => 's'],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->statusOptions = DoubleShiftRequestModel::statusList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== DoubleShiftRequestModel::STATUS_APPROVED) {
      $this->approvedBy = null;
      $this->approvedAt = null;
    }
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = DoubleShiftRequestModel::with(['karyawan', 'shiftAwal', 'shiftTambahan', 'approver'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('catatan', 'like', $like)
            ->orWhereHas('karyawan', function ($karyawanQuery) use ($like) {
              $karyawanQuery->where('nik', 'like', $like)
                ->orWhere('nama', 'like', $like);
            })
            ->orWhereHas('shiftAwal', function ($shiftQuery) use ($like) {
              $shiftQuery->where('nama', 'like', $like);
            })
            ->orWhereHas('shiftTambahan', function ($shiftQuery) use ($like) {
              $shiftQuery->where('nama', 'like', $like);
            })
            ->orWhereHas('approver', function ($approverQuery) use ($like) {
              $approverQuery->where('username', 'like', $like);
            });
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc');

    return view('livewire.pengajuan.double-shift', [
      'data'       => $query->paginate(10),
      'title'      => 'Pengajuan Double Shift',
      'hasActions' => auth()->user()->canAny([
        'pengajuan-double-shift.create',
        'pengajuan-double-shift.edit',
        'pengajuan-double-shift.delete',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('pengajuan-double-shift.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadShifts();
    $this->loadApprovers();

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('pengajuan-double-shift.edit');
    $this->resetValidation();

    $request = DoubleShiftRequestModel::find($id);
    if (!$request) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->requestId = $request->id;
    $this->karyawanId = $request->karyawan_id;
    $this->tanggal = $request->tanggal?->format('Y-m-d');
    $this->shiftAwalId = $request->shift_awal_id;
    $this->shiftTambahanId = $request->shift_tambahan_id;
    $this->catatan = $request->catatan;
    $this->status = $request->status;
    $this->approvedBy = $request->approved_by;
    $this->approvedAt = $request->approved_at?->format('Y-m-d\TH:i');
    $this->isEdit = true;

    $this->loadKaryawans();
    $this->loadShifts();
    $this->loadApprovers();

    $this->dispatch('openModal');
  }

  public function save(DoubleShiftRequestService $service)
  {
    $this->validate(
      [
        'karyawanId'     => ['required', 'exists:karyawan,id'],
        'tanggal'        => ['required', 'date'],
        'shiftAwalId'    => ['required', 'exists:shift_master,id'],
        'shiftTambahanId' => ['required', 'exists:shift_master,id', 'different:shiftAwalId'],
        'catatan'        => ['nullable', 'string', 'max:255'],
        'status'         => ['required', Rule::in(DoubleShiftRequestModel::statusList())],
        'approvedBy'     => [
          Rule::requiredIf($this->status === DoubleShiftRequestModel::STATUS_APPROVED),
          'nullable',
          'exists:user,id',
        ],
        'approvedAt'     => [
          Rule::requiredIf($this->status === DoubleShiftRequestModel::STATUS_APPROVED),
          'nullable',
          'date',
        ],
      ],
      [
        'karyawanId.required' => 'Karyawan wajib dipilih.',
        'tanggal.required' => 'Tanggal wajib diisi.',
        'shiftAwalId.required' => 'Shift awal wajib dipilih.',
        'shiftTambahanId.required' => 'Shift tambahan wajib dipilih.',
        'shiftTambahanId.different' => 'Shift tambahan tidak boleh sama dengan shift awal.',
        'approvedBy.required' => 'Approver wajib dipilih saat status approved.',
        'approvedAt.required' => 'Waktu approval wajib diisi saat status approved.',
      ]
    );

    $this->isEdit ? $this->updateData($service) : $this->storeData($service);
  }

  public function storeData(DoubleShiftRequestService $service)
  {
    $this->authorize('pengajuan-double-shift.create');

    try {
      $service->add($this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan double shift berhasil ditambah.'
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

  public function updateData(DoubleShiftRequestService $service)
  {
    $this->authorize('pengajuan-double-shift.edit');

    try {
      $service->update((int) $this->requestId, $this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan double shift berhasil diupdate.'
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
    $this->authorize('pengajuan-double-shift.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData(DoubleShiftRequestService $service)
  {
    $this->authorize('pengajuan-double-shift.delete');

    if (!$this->deleteId) {
      return;
    }

    try {
      $service->delete((int) $this->deleteId);
      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan double shift berhasil dihapus.'
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

  protected function loadShifts(): void
  {
    if (!empty($this->shifts)) {
      return;
    }

    $this->shifts = ShiftMaster::where('is_active', true)
      ->select('id', 'nama', 'jam_masuk', 'jam_pulang')
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

  protected function payload(): array
  {
    return [
      'karyawan_id'       => $this->karyawanId,
      'tanggal'           => $this->tanggal,
      'shift_awal_id'     => $this->shiftAwalId,
      'shift_tambahan_id' => $this->shiftTambahanId,
      'catatan'           => $this->catatan,
      'status'            => $this->status,
      'approved_by'       => $this->approvedBy,
      'approved_at'       => $this->approvedAt,
    ];
  }

  public function resetForm()
  {
    $this->reset([
      'requestId',
      'karyawanId',
      'tanggal',
      'shiftAwalId',
      'shiftTambahanId',
      'catatan',
      'approvedBy',
      'approvedAt',
    ]);

    $this->status = DoubleShiftRequestModel::STATUS_PENDING;
  }
}
