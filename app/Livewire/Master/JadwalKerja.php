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
  JadwalKaryawan as JadwalKaryawanModel,
  Karyawan,
  ShiftMaster,
  Holiday
};

#[Title('Jadwal Kerja')]
class JadwalKerja extends Component
{
  use AuthorizesRequests, WithPagination;

  public $jadwalId;
  public $karyawanId;
  public $tanggal;
  public $shiftId;
  public $shiftNama;
  public $jamMasuk;
  public $jamPulang;
  public $isLibur = false;
  public $isHoliday = false;
  public $generatedBy = 'manual';
  public $isEdit   = false;
  public $deleteId = null;

  public $karyawans = [];
  public $shifts = [];

  public $search = '';

  protected $queryString = [
    'search' => [
      'except' => '',
      'as'     => 's',
    ],
  ];

  protected $paginationTheme = 'bootstrap';

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function updatedTanggal($value)
  {
    if (!$value) {
      $this->isHoliday = false;
      return;
    }

    $this->isHoliday = Holiday::whereDate('tanggal', $value)->exists();
  }

  public function updatedShiftId($value)
  {
    if (!$value || $this->isLibur) {
      return;
    }

    $shift = ShiftMaster::find($value);
    if (!$shift) {
      return;
    }

    $this->shiftNama = $shift->nama;
    $this->jamMasuk = $shift->jam_masuk?->format('H:i');
    $this->jamPulang = $shift->jam_pulang?->format('H:i');
  }

  public function updatedIsLibur($value)
  {
    if ((bool) $value) {
      $this->shiftId = null;
      $this->shiftNama = null;
      $this->jamMasuk = null;
      $this->jamPulang = null;
    }
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = JadwalKaryawanModel::with(['karyawan', 'shift'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->where(function ($sub) use ($like) {
          $sub->where('tanggal', 'like', $like)
            ->orWhere('shift_nama', 'like', $like)
            ->orWhere('generated_by', 'like', $like)
            ->orWhereHas('karyawan', function ($karyawanQuery) use ($like) {
              $karyawanQuery->where('nik', 'like', $like)
                ->orWhere('nama', 'like', $like);
            })
            ->orWhereHas('shift', function ($shiftQuery) use ($like) {
              $shiftQuery->where('nama', 'like', $like);
            });
        });
      })
      ->orderBy('tanggal', 'desc')
      ->orderBy('id', 'desc');

    return view('livewire.master.jadwal-kerja', [
      'data'       => $query->paginate(10),
      'title'      => 'Jadwal Kerja',
      'hasActions' => auth()->user()->canAny([
        'jadwal-kerja.edit',
        'jadwal-kerja.delete',
        'jadwal-kerja.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('jadwal-kerja.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->loadKaryawans();
    $this->loadShifts();

    $this->dispatch(
      'openModal',
      karyawan_options: $this->karyawanOptions(),
      shift_options: $this->shiftOptions()
    );
  }

  public function edit($id)
  {
    $this->authorize('jadwal-kerja.edit');

    $this->resetValidation();

    $jadwal = JadwalKaryawanModel::find($id);
    if (!$jadwal) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->jadwalId    = $id;
    $this->karyawanId  = $jadwal->karyawan_id;
    $this->tanggal     = $jadwal->tanggal?->format('Y-m-d');
    $this->shiftId     = $jadwal->shift_id;
    $this->shiftNama   = $jadwal->shift_nama;
    $this->jamMasuk    = $jadwal->jam_masuk?->format('H:i');
    $this->jamPulang   = $jadwal->jam_pulang?->format('H:i');
    $this->isLibur     = (bool) $jadwal->is_libur;
    $this->isHoliday   = (bool) $jadwal->is_holiday;
    $this->generatedBy = $jadwal->generated_by ?: 'manual';
    $this->isEdit      = true;

    $this->loadKaryawans();
    $this->loadShifts();

    $this->dispatch(
      'openModal',
      karyawan_options: $this->karyawanOptions(),
      shift_options: $this->shiftOptions(),
      karyawan_id: $this->karyawanId,
      shift_id: $this->shiftId
    );
  }

  public function save()
  {
    $this->validate(
      [
        'karyawanId' => ['required', 'exists:karyawan,id'],
        'tanggal'    => [
          'required',
          'date',
          Rule::unique('jadwal_karyawan', 'tanggal')
            ->where(fn($q) => $q->where('karyawan_id', $this->karyawanId))
            ->ignore($this->jadwalId),
        ],
        'shiftId'    => ['nullable', 'exists:shift_master,id'],
        'shiftNama'  => ['nullable', 'string', 'max:50'],
        'jamMasuk'   => ['nullable', 'date_format:H:i'],
        'jamPulang'  => ['nullable', 'date_format:H:i'],
        'isLibur'    => ['boolean'],
        'isHoliday'  => ['boolean'],
        'generatedBy' => ['nullable', 'string', 'max:30'],
      ],
      [
        'karyawanId.required' => 'Karyawan wajib dipilih.',
        'tanggal.required'    => 'Tanggal wajib diisi.',
        'tanggal.unique'      => 'Jadwal untuk karyawan dan tanggal tersebut sudah ada.',
        'jamMasuk.date_format' => 'Format jam masuk tidak valid (HH:MM).',
        'jamPulang.date_format' => 'Format jam pulang tidak valid (HH:MM).',
      ]
    );

    if (!$this->isLibur && !$this->shiftId && (!$this->jamMasuk || !$this->jamPulang)) {
      $this->addError('jamMasuk', 'Jam masuk dan jam pulang wajib diisi jika tidak memilih shift.');
      return;
    }

    $this->normalizeFormValues();

    $this->isEdit ? $this->updateData() : $this->storeData();
  }

  public function storeData()
  {
    $this->authorize('jadwal-kerja.create');

    try {
      JadwalKaryawanModel::create([
        'karyawan_id' => $this->karyawanId,
        'tanggal'     => $this->tanggal,
        'shift_id'    => $this->shiftId,
        'shift_nama'  => $this->shiftNama,
        'jam_masuk'   => $this->jamMasuk,
        'jam_pulang'  => $this->jamPulang,
        'is_libur'    => (bool) $this->isLibur,
        'is_holiday'  => (bool) $this->isHoliday,
        'generated_by' => $this->generatedBy ?: 'manual',
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
    $this->authorize('jadwal-kerja.edit');

    try {
      JadwalKaryawanModel::find($this->jadwalId)?->update([
        'karyawan_id' => $this->karyawanId,
        'tanggal'     => $this->tanggal,
        'shift_id'    => $this->shiftId,
        'shift_nama'  => $this->shiftNama,
        'jam_masuk'   => $this->jamMasuk,
        'jam_pulang'  => $this->jamPulang,
        'is_libur'    => (bool) $this->isLibur,
        'is_holiday'  => (bool) $this->isHoliday,
        'generated_by' => $this->generatedBy ?: 'manual',
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
    $this->authorize('jadwal-kerja.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('jadwal-kerja.delete');

    if ($this->deleteId) {
      try {
        JadwalKaryawanModel::destroy($this->deleteId);
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

  protected function loadShifts(): void
  {
    if (!empty($this->shifts)) {
      return;
    }

    $this->shifts = ShiftMaster::query()
      ->select('id', 'nama', 'jam_masuk', 'jam_pulang', 'is_active')
      ->orderByDesc('is_active')
      ->orderBy('nama')
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

  protected function shiftOptions(): array
  {
    return collect($this->shifts)
      ->map(fn($shift) => [
        'id'         => $shift->id,
        'nama'       => $shift->nama,
        'jam_masuk'  => $shift->jam_masuk?->format('H:i'),
        'jam_pulang' => $shift->jam_pulang?->format('H:i'),
        'is_active'  => (bool) $shift->is_active,
      ])
      ->values()
      ->all();
  }

  protected function normalizeFormValues(): void
  {
    $holidayFromMaster = $this->tanggal
      ? Holiday::whereDate('tanggal', $this->tanggal)->exists()
      : false;

    $this->isHoliday = $holidayFromMaster || (bool) $this->isHoliday;

    if ($this->isLibur) {
      $this->shiftId = null;
      $this->shiftNama = null;
      $this->jamMasuk = null;
      $this->jamPulang = null;
      return;
    }

    if ($this->shiftId) {
      $shift = ShiftMaster::find($this->shiftId);
      if ($shift) {
        $this->shiftNama = $shift->nama;
        $this->jamMasuk = $shift->jam_masuk?->format('H:i');
        $this->jamPulang = $shift->jam_pulang?->format('H:i');
      }
    } else {
      $this->shiftNama = $this->shiftNama ? trim($this->shiftNama) : null;
    }

    $this->generatedBy = $this->generatedBy ? trim(strtolower($this->generatedBy)) : 'manual';
  }

  public function resetForm()
  {
    $this->reset([
      'jadwalId',
      'karyawanId',
      'tanggal',
      'shiftId',
      'shiftNama',
      'jamMasuk',
      'jamPulang',
    ]);

    $this->isLibur = false;
    $this->isHoliday = false;
    $this->generatedBy = 'manual';
  }
}
