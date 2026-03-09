<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{
  JadwalLembur as JadwalLemburModel,
  Karyawan,
  User
};

#[Title('Jadwal Lembur')]
class JadwalLembur extends Component
{
  use AuthorizesRequests, WithPagination;

  public $lemburId;
  public $karyawanId;
  public $tanggal;
  public $type = JadwalLemburModel::TYPE_NORMAL;
  public $jamMulai;
  public $jamSelesai;
  public $status = JadwalLemburModel::STATUS_PENDING;
  public $approvedBy;
  public $approvedAt;
  public $isEdit   = false;
  public $deleteId = null;

  public $karyawans = [];
  public $approvers = [];
  public $statusOptions = [];
  public $typeOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => [
      'except' => '',
      'as'     => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->statusOptions = JadwalLemburModel::statusList();
    $this->typeOptions = JadwalLemburModel::typeList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== JadwalLemburModel::STATUS_APPROVED) {
      $this->approvedBy = null;
      $this->approvedAt = null;
    }
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = JadwalLemburModel::with(['karyawan', 'approver'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('type', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->orWhereHas('karyawan', function ($karyawanQuery) use ($like) {
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

    return view('livewire.master.jadwal-lembur', [
      'data'       => $query->paginate(10),
      'title'      => 'Jadwal Lembur',
      'hasActions' => auth()->user()->canAny([
        'jadwal-lembur.edit',
        'jadwal-lembur.delete',
        'jadwal-lembur.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('jadwal-lembur.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadApprovers();

    $this->dispatch(
      'openModal',
      karyawan_options: $this->karyawanOptions(),
      approver_options: $this->approverOptions()
    );
  }

  public function edit($id)
  {
    $this->authorize('jadwal-lembur.edit');

    $this->resetValidation();

    $lembur = JadwalLemburModel::find($id);
    if (!$lembur) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->lemburId    = $id;
    $this->karyawanId  = $lembur->karyawan_id;
    $this->tanggal     = $lembur->tanggal?->format('Y-m-d');
    $this->type        = $lembur->type;
    $this->jamMulai    = $lembur->jam_mulai?->format('H:i');
    $this->jamSelesai  = $lembur->jam_selesai?->format('H:i');
    $this->status      = $lembur->status;
    $this->approvedBy  = $lembur->approved_by;
    $this->approvedAt  = $lembur->approved_at?->format('Y-m-d\TH:i');
    $this->isEdit      = true;

    $this->loadKaryawans();
    $this->loadApprovers();

    $this->dispatch(
      'openModal',
      karyawan_options: $this->karyawanOptions(),
      approver_options: $this->approverOptions(),
      karyawan_id: $this->karyawanId,
      approved_by: $this->approvedBy
    );
  }

  public function save()
  {
    $this->validate(
      [
        'karyawanId' => ['required', 'exists:karyawan,id'],
        'tanggal'    => ['required', 'date'],
        'type'       => ['required', Rule::in(JadwalLemburModel::typeList())],
        'jamMulai'   => ['required', 'date_format:H:i'],
        'jamSelesai' => ['required', 'date_format:H:i'],
        'status'     => ['required', Rule::in(JadwalLemburModel::statusList())],
        'approvedBy' => [
          Rule::requiredIf($this->status === JadwalLemburModel::STATUS_APPROVED),
          'nullable',
          'exists:user,id',
        ],
        'approvedAt' => [
          Rule::requiredIf($this->status === JadwalLemburModel::STATUS_APPROVED),
          'nullable',
          'date',
        ],
      ],
      [
        'karyawanId.required' => 'Karyawan wajib dipilih.',
        'tanggal.required'    => 'Tanggal wajib diisi.',
        'type.required'       => 'Tipe lembur wajib dipilih.',
        'jamMulai.required'   => 'Jam mulai wajib diisi.',
        'jamSelesai.required' => 'Jam selesai wajib diisi.',
        'status.required'     => 'Status wajib dipilih.',
        'approvedBy.required' => 'Approver wajib dipilih saat status approved.',
        'approvedAt.required' => 'Waktu approval wajib diisi saat status approved.',
      ]
    );

    if ($this->jamMulai === $this->jamSelesai) {
      $this->addError('jamSelesai', 'Jam selesai tidak boleh sama dengan jam mulai.');
      return;
    }

    $this->normalizeApprovalValues();

    $this->isEdit ? $this->updateData() : $this->storeData();
  }

  public function storeData()
  {
    $this->authorize('jadwal-lembur.create');

    try {
      JadwalLemburModel::create([
        'karyawan_id' => $this->karyawanId,
        'tanggal'     => $this->tanggal,
        'type'        => $this->type,
        'jam_mulai'   => $this->jamMulai,
        'jam_selesai' => $this->jamSelesai,
        'status'      => $this->status,
        'approved_by' => $this->approvedBy,
        'approved_at' => $this->approvedAt,
        'created_by'  => auth()->id(),
        'updated_by'  => auth()->id(),
      ]);

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);

      $this->resetForm();
      $this->dispatch('closeModal');
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal ditambah.'
      ]);
    }
  }

  public function updateData()
  {
    $this->authorize('jadwal-lembur.edit');

    try {
      JadwalLemburModel::find($this->lemburId)?->update([
        'karyawan_id' => $this->karyawanId,
        'tanggal'     => $this->tanggal,
        'type'        => $this->type,
        'jam_mulai'   => $this->jamMulai,
        'jam_selesai' => $this->jamSelesai,
        'status'      => $this->status,
        'approved_by' => $this->approvedBy,
        'approved_at' => $this->approvedAt,
        'updated_by'  => auth()->id(),
      ]);

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);

      $this->resetForm();
      $this->dispatch('closeModal');
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal diupdate.'
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('jadwal-lembur.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('jadwal-lembur.delete');

    if ($this->deleteId) {
      try {
        JadwalLemburModel::destroy($this->deleteId);
        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil dihapus.'
        ]);
        $this->deleteId = null;
        $this->dispatch('closeConfirmModal');
      } catch (\Exception $e) {
        $this->dispatch('closeConfirmModal');
        if (str_contains($e->getMessage(), 'Integrity constraint violation')) {
          $this->dispatch('alert', [
            'type'    => 'error',
            'message' => 'Data tidak bisa dihapus karena masih digunakan.'
          ]);
        } else {
          $this->dispatch('alert', [
            'type'    => 'error',
            'message' => 'Terjadi kesalahan saat menghapus data.'
          ]);
        }
      }
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

    $this->approvers = User::with('karyawan')
      ->select('id', 'username', 'karyawan_id')
      ->orderBy('username')
      ->get();
  }

  protected function karyawanOptions(): array
  {
    return collect($this->karyawans)
      ->map(fn($karyawan) => [
        'id'   => $karyawan->id,
        'nik'  => $karyawan->nik,
        'nama' => $karyawan->nama,
      ])
      ->values()
      ->all();
  }

  protected function approverOptions(): array
  {
    return collect($this->approvers)
      ->map(fn($user) => [
        'id'       => $user->id,
        'username' => $user->username,
        'nama'     => $user->karyawan?->nama,
      ])
      ->values()
      ->all();
  }

  protected function normalizeApprovalValues(): void
  {
    if ($this->status === JadwalLemburModel::STATUS_APPROVED) {
      $this->approvedBy = $this->approvedBy ?: auth()->id();
      $this->approvedAt = $this->approvedAt
        ? date('Y-m-d H:i:s', strtotime($this->approvedAt))
        : now()->format('Y-m-d H:i:s');
      return;
    }

    $this->approvedBy = null;
    $this->approvedAt = null;
  }

  public function resetForm()
  {
    $this->reset([
      'lemburId',
      'karyawanId',
      'tanggal',
      'jamMulai',
      'jamSelesai',
      'approvedBy',
      'approvedAt',
    ]);

    $this->type = JadwalLemburModel::TYPE_NORMAL;
    $this->status = JadwalLemburModel::STATUS_PENDING;
  }
}
