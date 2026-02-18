<?php

namespace App\Livewire\Absensi;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{
  Absensi,
  Karyawan
};

use App\Services\Absensi\AbsensiService;
use App\Services\Log\AbsensiLogger;

#[Title('Absensi')]
class Index extends Component
{
  use AuthorizesRequests, WithPagination;

  public $absenId;
  public $karyawanId;
  public $tanggal;
  public $status = 'hadir';
  public $masuk;
  public $pulang;
  public $source = 'manual';
  public $keteranganMasuk;
  public $keteranganPulang;
  public $isEdit    = false;
  public $deleteId  = null;

  public $karyawans = [];
  public $statusOptions = [];

  public $search = '';

  protected $queryString = [
    'search' => [
      'except'  => '',
      'as'      => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function mount()
  {
    $this->karyawans = Karyawan::bolehAbsen()
      ->select('id', 'nik', 'nama')
      ->orderBy('id')
      ->get();

    $this->statusOptions = Absensi::statusList();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedStatus($value)
  {
    if ($value !== 'hadir') {
      $this->jam_masuk = null;
      $this->jam_pulang = null;
    }
  }

  public function render()
  {
    $query = Absensi::with([
      'karyawan',
      'lastLog.inputBy',
    ])
      ->when($this->search, function ($q) {
        $q->where(function ($sub) {
          $sub->where('tanggal', 'like', "{$this->search}%")
            ->orWhereHas('karyawan', function ($d) {
              $d->where('nik', 'like', "{$this->search}%")
                ->orWhere('nama', 'like', "{$this->search}%");
            })
            ->orWhereHas('lastLog', function ($l) {
              $l->where('jenis', 'like', "{$this->search}%")
                ->orWhere('source', 'like', "{$this->search}%")
                ->orWhere('keterangan', 'like', "{$this->search}%");
            });
        });
      })
      ->orderBy('tanggal', 'desc');

    return view('livewire.absensi.index', [
      'data'        => $query->paginate(10),
      'title'       => 'Absensi',
      'hasActions'  => auth()->user()->canAny([
        'absensi.edit',
        'absensi.delete',
        'absensi.create',
        'absensi.detail',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('absensi.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('absensi.edit');

    $this->resetValidation();

    $absensi = Absensi::with('logs')->find($id);

    if (!$absensi) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $logMasuk = $absensi->logs
      ->where('jenis', 'masuk')
      ->sortByDesc('id')
      ->first();

    $logPulang = $absensi->logs
      ->where('jenis', 'pulang')
      ->sortByDesc('id')
      ->first();

    $this->karyawanId = $absensi->karyawan_id;
    $this->tanggal    = $absensi->tanggal?->format('Y-m-d');
    $this->status     = $absensi->status;
    $this->masuk      = $absensi->jam_masuk?->format('H:i');
    $this->pulang     = $absensi->jam_pulang?->format('H:i');

    $this->keteranganMasuk  = $logMasuk?->keterangan;
    $this->keteranganPulang = $logPulang?->keterangan;

    $this->absenId = $id;
    $this->isEdit  = true;

    $this->dispatch('openModal', karyawan_id: $this->karyawanId);
  }

  public function save(AbsensiService $service)
  {
    $this->validate(
      [
        'karyawanId'        => ['required', 'exists:karyawan,id'],
        'tanggal'           => ['required', 'date'],
        'status'            => ['required', Rule::in(Absensi::statusList())],
        'masuk'             => [Rule::requiredIf($this->status === 'hadir'), 'nullable', 'date_format:H:i'],
        'keteranganMasuk'   => ['required_with:masuk'],
        'pulang'            => ['nullable', 'date_format:H:i'],
        'keteranganPulang'  => ['required_with:pulang'],
      ],
      [
        'karyawanId.required' => 'Karyawan wajib dipilih.',
        'tanggal.required'    => 'Tanggal wajib diisi.',
        'masuk.required'      => 'Jam masuk wajib diisi.',
        'keteranganMasuk.required_with'  => 'Keterangan masuk wajib diisi jika jam masuk diisi.',
        'keteranganPulang.required_with' => 'Keterangan pulang wajib diisi jika jam pulang diisi.',
      ]
    );

    if ($this->masuk && $this->pulang && $this->pulang <= $this->masuk) {
      $this->addError('pulang', 'Jam pulang harus lebih besar dari jam masuk.');
      return;
    }

    if (!$this->status) {
      $this->addError('masuk', 'Minimal isi status kedatangan');
      return;
    }

    $this->isEdit ? $this->updateData($service) : $this->storeData($service);
  }

  public function storeData(AbsensiService $service)
  {
    $this->authorize('absensi.create');

    try {
      $exists = Absensi::where('karyawan_id', $this->karyawanId)
        ->whereDate('tanggal', $this->tanggal)
        ->exists();

      if ($exists) {
        $this->addError('tanggal', 'Absensi untuk tanggal tersebut sudah ada.');
        return;
      }

      $service->store(
        $this->source,
        [
          'karyawan_id'       => $this->karyawanId,
          'tanggal'           => $this->tanggal,
          'status'            => $this->status,
          'jam_masuk'         => $this->masuk,
          'jam_pulang'        => $this->pulang,
          'keterangan_masuk'  => $this->keteranganMasuk,
          'keterangan_pulang' => $this->keteranganPulang,
        ]
      );

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);

      $this->resetForm();
      $this->dispatch('reset-select');
      $this->dispatch('closeModal');
    } catch (\Throwable $e) {
      AbsensiLogger::error('Create absensi gagal', [
        'karyawan_id' => $this->karyawanId,
        'tanggal'     => $this->tanggal,
        'error'       => $e->getMessage(),
      ]);

      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Terjadi kesalahan saat menyimpan data.'
      ]);
    }
  }

  public function updateData(AbsensiService $service)
  {
    $this->authorize('absensi.edit');

    try {
      $absen = Absensi::findOrFail($this->absenId);

      $exists = Absensi::where('karyawan_id', $this->karyawanId)
        ->whereDate('tanggal', $this->tanggal)
        ->where('id', '!=', $absen->id)
        ->exists();

      if ($exists) {
        $this->addError('tanggal', 'Absensi untuk tanggal tersebut sudah ada.');
        return;
      }

      $service->update(
        $absen->id,
        $this->source,
        [
          'karyawan_id'       => $this->karyawanId,
          'tanggal'           => $this->tanggal,
          'status'            => $this->status,
          'jam_masuk'         => $this->masuk,
          'jam_pulang'        => $this->pulang,
          'keterangan_masuk'  => $this->keteranganMasuk,
          'keterangan_pulang' => $this->keteranganPulang,
        ]
      );

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);

      $this->resetForm();
      $this->dispatch('closeModal');
    } catch (\Throwable $e) {
      AbsensiLogger::error('Update absensi gagal', [
        'absensi_id' => $this->absenId,
        'error'      => $e->getMessage(),
      ]);

      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Terjadi kesalahan saat mengupdate data.'
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('absensi.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('absensi.delete');

    if ($this->deleteId) {
      try {
        Absensi::destroy($this->deleteId);
        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil dihapus.'
        ]);
        $this->deleteId = null;
        $this->dispatch('closeConfirmModal');
      } catch (\Exception $e) {
        AbsensiLogger::error('Delete absensi gagal', [
          'absensi_id' => $this->deleteId,
          'error'      => $e->getMessage(),
        ]);

        $this->dispatch('closeConfirmModal');
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Terjadi kesalahan saat menghapus data.'
        ]);
      }
    }
  }

  public function resetForm()
  {
    $this->reset([
      'karyawanId',
      'tanggal',
      'masuk',
      'pulang',
      'keteranganMasuk',
      'keteranganPulang',
    ]);
  }
}
