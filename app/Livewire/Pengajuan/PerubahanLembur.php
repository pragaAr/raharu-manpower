<?php

namespace App\Livewire\Pengajuan;

use App\Models\JadwalLembur;
use App\Models\Karyawan;
use App\Models\PerubahanLemburRequest as PerubahanLemburRequestModel;
use App\Models\User;
use App\Services\Pengajuan\PerubahanLemburRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengajuan Perubahan Lembur')]
class PerubahanLembur extends Component
{
  use AuthorizesRequests, WithPagination;

  public $requestId;
  public $karyawanId;
  public $jadwalLemburId;
  public $tanggal;
  public $jamMulaiLama;
  public $jamSelesaiLama;
  public $jamMulaiBaru;
  public $jamSelesaiBaru;
  public $alasan;
  public $status = PerubahanLemburRequestModel::STATUS_PENDING;
  public $approvedBy;
  public $approvedAt;
  public $isEdit = false;
  public $deleteId = null;

  public $karyawans = [];
  public $jadwalLemburs = [];
  public $approvers = [];
  public $statusOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => ['except' => '', 'as' => 's'],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->statusOptions = PerubahanLemburRequestModel::statusList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== PerubahanLemburRequestModel::STATUS_APPROVED) {
      $this->approvedBy = null;
      $this->approvedAt = null;
    }
  }

  public function updatedKaryawanId()
  {
    $this->jadwalLemburId = null;
    $this->tanggal = null;
    $this->jamMulaiLama = null;
    $this->jamSelesaiLama = null;
    $this->loadJadwalLemburs();
  }

  public function updatedJadwalLemburId()
  {
    $jadwal = collect($this->jadwalLemburs)->firstWhere('id', (int) $this->jadwalLemburId);

    if (!$jadwal) {
      $this->tanggal = null;
      $this->jamMulaiLama = null;
      $this->jamSelesaiLama = null;
      return;
    }

    $this->tanggal = $jadwal->tanggal?->format('Y-m-d');
    $this->jamMulaiLama = $jadwal->jam_mulai?->format('H:i');
    $this->jamSelesaiLama = $jadwal->jam_selesai?->format('H:i');

    if (!$this->jamMulaiBaru) {
      $this->jamMulaiBaru = $this->jamMulaiLama;
    }

    if (!$this->jamSelesaiBaru) {
      $this->jamSelesaiBaru = $this->jamSelesaiLama;
    }
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = PerubahanLemburRequestModel::with(['karyawan', 'jadwalLembur', 'approver'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
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
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc');

    return view('livewire.pengajuan.perubahan-lembur', [
      'data'       => $query->paginate(10),
      'title'      => 'Pengajuan Perubahan Lembur',
      'hasActions' => auth()->user()->canAny([
        'pengajuan-lembur.create',
        'pengajuan-lembur.edit',
        'pengajuan-lembur.delete',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('pengajuan-lembur.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadApprovers();
    $this->loadJadwalLemburs();

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('pengajuan-lembur.edit');
    $this->resetValidation();

    $request = PerubahanLemburRequestModel::find($id);
    if (!$request) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->requestId = $request->id;
    $this->karyawanId = $request->karyawan_id;
    $this->jadwalLemburId = $request->jadwal_lembur_id;
    $this->tanggal = $request->tanggal?->format('Y-m-d');
    $this->jamMulaiLama = $request->jam_mulai_lama?->format('H:i');
    $this->jamSelesaiLama = $request->jam_selesai_lama?->format('H:i');
    $this->jamMulaiBaru = $request->jam_mulai_baru?->format('H:i');
    $this->jamSelesaiBaru = $request->jam_selesai_baru?->format('H:i');
    $this->alasan = $request->alasan;
    $this->status = $request->status;
    $this->approvedBy = $request->approved_by;
    $this->approvedAt = $request->approved_at?->format('Y-m-d\TH:i');
    $this->isEdit = true;

    $this->loadKaryawans();
    $this->loadApprovers();
    $this->loadJadwalLemburs();

    $this->dispatch('openModal');
  }

  public function save(PerubahanLemburRequestService $service)
  {
    $this->validate(
      [
        'karyawanId'     => ['required', 'exists:karyawan,id'],
        'jadwalLemburId' => ['required', 'exists:jadwal_lembur,id'],
        'jamMulaiBaru'   => ['required', 'date_format:H:i'],
        'jamSelesaiBaru' => ['required', 'date_format:H:i', 'after:jamMulaiBaru'],
        'alasan'         => ['nullable', 'string', 'max:255'],
        'status'         => ['required', Rule::in(PerubahanLemburRequestModel::statusList())],
        'approvedBy'     => [
          Rule::requiredIf($this->status === PerubahanLemburRequestModel::STATUS_APPROVED),
          'nullable',
          'exists:user,id',
        ],
        'approvedAt'     => [
          Rule::requiredIf($this->status === PerubahanLemburRequestModel::STATUS_APPROVED),
          'nullable',
          'date',
        ],
      ],
      [
        'karyawanId.required' => 'Karyawan wajib dipilih.',
        'jadwalLemburId.required' => 'Jadwal lembur wajib dipilih.',
        'jamMulaiBaru.required' => 'Jam mulai baru wajib diisi.',
        'jamSelesaiBaru.required' => 'Jam selesai baru wajib diisi.',
        'jamSelesaiBaru.after' => 'Jam selesai baru harus lebih besar dari jam mulai baru.',
        'approvedBy.required' => 'Approver wajib dipilih saat status approved.',
        'approvedAt.required' => 'Waktu approval wajib diisi saat status approved.',
      ]
    );

    $this->isEdit ? $this->updateData($service) : $this->storeData($service);
  }

  public function storeData(PerubahanLemburRequestService $service)
  {
    $this->authorize('pengajuan-lembur.create');

    try {
      $service->add($this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan perubahan lembur berhasil ditambah.'
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

  public function updateData(PerubahanLemburRequestService $service)
  {
    $this->authorize('pengajuan-lembur.edit');

    try {
      $service->update((int) $this->requestId, $this->payload());

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan perubahan lembur berhasil diupdate.'
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
    $this->authorize('pengajuan-lembur.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData(PerubahanLemburRequestService $service)
  {
    $this->authorize('pengajuan-lembur.delete');

    if (!$this->deleteId) {
      return;
    }

    try {
      $service->delete((int) $this->deleteId);
      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Pengajuan perubahan lembur berhasil dihapus.'
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

  protected function loadJadwalLemburs(): void
  {
    $this->jadwalLemburs = $this->karyawanId
      ? JadwalLembur::where('karyawan_id', $this->karyawanId)
      ->select('id', 'tanggal', 'jam_mulai', 'jam_selesai')
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc')
      ->get()
      : [];
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
      'karyawan_id'      => $this->karyawanId,
      'jadwal_lembur_id' => $this->jadwalLemburId,
      'jam_mulai_baru'   => $this->jamMulaiBaru,
      'jam_selesai_baru' => $this->jamSelesaiBaru,
      'alasan'           => $this->alasan,
      'status'           => $this->status,
      'approved_by'      => $this->approvedBy,
      'approved_at'      => $this->approvedAt,
    ];
  }

  public function resetForm()
  {
    $this->reset([
      'requestId',
      'karyawanId',
      'jadwalLemburId',
      'tanggal',
      'jamMulaiLama',
      'jamSelesaiLama',
      'jamMulaiBaru',
      'jamSelesaiBaru',
      'alasan',
      'approvedBy',
      'approvedAt',
    ]);

    $this->status = PerubahanLemburRequestModel::STATUS_PENDING;
    $this->jadwalLemburs = [];
  }
}
