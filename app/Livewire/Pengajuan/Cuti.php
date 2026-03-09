<?php

namespace App\Livewire\Pengajuan;

use App\Models\CutiRequest as CutiRequestModel;
use App\Models\Karyawan;
use App\Models\User;
use App\Services\Pengajuan\CutiRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengajuan Cuti')]
class Cuti extends Component
{
  use AuthorizesRequests, WithPagination;

  public $requestId;
  public $karyawanId;
  public $tanggalMulai;
  public $tanggalSelesai;
  public $alasan;
  public $status = CutiRequestModel::STATUS_PENDING;
  public $approvedBy;
  public $approvedAt;
  public $isEdit = false;
  public $deleteId = null;

  public $karyawans = [];
  public $approvers = [];
  public $statusOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => ['except' => '', 'as' => 's'],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->statusOptions = CutiRequestModel::statusList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== CutiRequestModel::STATUS_APPROVED) {
      $this->approvedBy = null;
      $this->approvedAt = null;
    }
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = CutiRequestModel::with(['karyawan', 'approver'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal_mulai', 'like', $like)
            ->orWhere('tanggal_selesai', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhere('alasan', 'like', $like)
            ->orWhereHas('karyawan', function ($karyawanQuery) use ($like) {
              $karyawanQuery->where('nik', 'like', $like)
                ->orWhere('nama', 'like', $like);
            })
            ->orWhereHas('approver', function ($approverQuery) use ($like) {
              $approverQuery->where('username', 'like', $like);
            });
        });
      })
      ->orderBy('tanggal_mulai', 'desc')
      ->orderBy('id', 'desc');

    return view('livewire.pengajuan.cuti', [
      'data'       => $query->paginate(10),
      'title'      => 'Pengajuan Cuti',
      'hasActions' => auth()->user()->canAny([
        'pengajuan-cuti.create',
        'pengajuan-cuti.edit',
        'pengajuan-cuti.delete',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('pengajuan-cuti.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadApprovers();

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('pengajuan-cuti.edit');
    $this->resetValidation();

    $request = CutiRequestModel::find($id);
    if (!$request) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->requestId = $request->id;
    $this->karyawanId = $request->karyawan_id;
    $this->tanggalMulai = $request->tanggal_mulai?->format('Y-m-d');
    $this->tanggalSelesai = $request->tanggal_selesai?->format('Y-m-d');
    $this->alasan = $request->alasan;
    $this->status = $request->status;
    $this->approvedBy = $request->approved_by;
    $this->approvedAt = $request->approved_at?->format('Y-m-d\TH:i');
    $this->isEdit = true;

    $this->loadKaryawans();
    $this->loadApprovers();

    $this->dispatch('openModal');
  }

  public function save(CutiRequestService $service)
  {
    $this->validate(
      [
        'karyawanId'     => ['required', 'exists:karyawan,id'],
        'tanggalMulai'   => ['required', 'date'],
        'tanggalSelesai' => ['required', 'date', 'after_or_equal:tanggalMulai'],
        'alasan'         => ['nullable', 'string', 'max:255'],
        'status'         => ['required', Rule::in(CutiRequestModel::statusList())],
        'approvedBy'     => [
          Rule::requiredIf($this->status === CutiRequestModel::STATUS_APPROVED),
          'nullable',
          'exists:user,id',
        ],
        'approvedAt'     => [
          Rule::requiredIf($this->status === CutiRequestModel::STATUS_APPROVED),
          'nullable',
          'date',
        ],
      ],
      [
        'karyawanId.required'     => 'Karyawan wajib dipilih.',
        'tanggalMulai.required'   => 'Tanggal mulai wajib diisi.',
        'tanggalSelesai.required' => 'Tanggal selesai wajib diisi.',
        'tanggalSelesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        'approvedBy.required'     => 'Approver wajib dipilih saat status approved.',
        'approvedAt.required'     => 'Waktu approval wajib diisi saat status approved.',
      ]
    );

    $this->isEdit ? $this->updateData($service) : $this->storeData($service);
  }

  public function storeData(CutiRequestService $service)
  {
    $this->authorize('pengajuan-cuti.create');

    try {
      $service->add($this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan cuti berhasil ditambah.'
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

  public function updateData(CutiRequestService $service)
  {
    $this->authorize('pengajuan-cuti.edit');

    try {
      $service->update((int) $this->requestId, $this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan cuti berhasil diupdate.'
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
    $this->authorize('pengajuan-cuti.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData(CutiRequestService $service)
  {
    $this->authorize('pengajuan-cuti.delete');

    if (!$this->deleteId) {
      return;
    }

    try {
      $service->delete((int) $this->deleteId);
      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan cuti berhasil dihapus.'
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

  protected function payload(): array
  {
    return [
      'karyawan_id'     => $this->karyawanId,
      'tanggal_mulai'   => $this->tanggalMulai,
      'tanggal_selesai' => $this->tanggalSelesai,
      'alasan'          => $this->alasan,
      'status'          => $this->status,
      'approved_by'     => $this->approvedBy,
      'approved_at'     => $this->approvedAt,
    ];
  }

  public function resetForm()
  {
    $this->reset([
      'requestId',
      'karyawanId',
      'tanggalMulai',
      'tanggalSelesai',
      'alasan',
      'approvedBy',
      'approvedAt',
    ]);

    $this->status = CutiRequestModel::STATUS_PENDING;
  }
}
