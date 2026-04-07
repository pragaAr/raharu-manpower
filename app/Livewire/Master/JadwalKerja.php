<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{
  JadwalKaryawan as JadwalKaryawanModel,
  Karyawan,
  Lokasi,
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
  public $lokasis = [];

  public $search = '';
  public $viewMode = 'all';
  public $generateMonth;
  public $generateLokasiId;
  public $previewReady = false;
  public $previewMonth;
  public $previewSummary = [];
  public $previewOffByDate = [];
  public $previewLiburBySatpam = [];
  public $previewMinLiburByLokasi = [];
  public $previewOffBySatpam = [];
  public $previewJadwalBySatpam = [];
  public $previewGridDays = [];
  public $previewJadwalGrid = [];
  public $previewPlanOffByDate = [];
  public $previewPlanLokasiId;

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

  public function updatedViewMode()
  {
    $this->resetPage();
  }

  public function updatedGenerateMonth()
  {
    $this->resetGeneratePreview();
  }

  public function updatedGenerateLokasiId()
  {
    $this->resetGeneratePreview();
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
      ->when($this->viewMode === 'rolling', function ($q) {
        $q->whereHas('karyawan.workRule', function ($ruleQuery) {
          $ruleQuery->where('use_shift', true);
        });
      })
      ->when($this->viewMode === 'fixed', function ($q) {
        $q->where(function ($sub) {
          $sub->whereHas('karyawan.workRule', function ($ruleQuery) {
            $ruleQuery->where('use_shift', false);
          })->orWhereDoesntHave('karyawan.workRule');
        });
      })
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

    return view('livewire.master.jadwal.kerja.index', [
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

    if (!$this->validateSatpamLiburRules()) {
      return;
    }

    $this->generatedBy = 'manual';
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

  public function openGenerateModal()
  {
    $this->authorize('jadwal-kerja.create');

    $this->resetGeneratePreview();
    $this->loadLokasis();
    $this->generateMonth = $this->defaultGenerateMonth();
    if (!$this->generateLokasiId && !empty($this->lokasis)) {
      $this->generateLokasiId = $this->lokasis[0]->id;
    }

    $this->dispatch(
      'openGenerateModal',
      lokasi_id: $this->generateLokasiId,
      lokasi_options: $this->lokasiOptions()
    );
  }

  public function cancelGenerateJadwal()
  {
    $this->resetGeneratePreview();
    $this->dispatch('closeGenerateModal');
  }

  public function previewGenerateJadwalSatpam()
  {
    $this->authorize('jadwal-kerja.create');

    $this->resetGeneratePreview();
    $inputs = $this->validateGenerateInputs();
    if (!$inputs) {
      return;
    }

    $plan = $this->buildSatpamPlan($inputs['start'], $inputs['lokasi_id']);
    if (!$plan) {
      return;
    }

    $this->previewMonth = $this->generateMonth;
    $this->previewReady = true;
    $this->previewSummary = $plan['summary'];
    $this->previewOffByDate = $plan['offByDatePreview'];
    $this->previewLiburBySatpam = $plan['liburBySatpam'];
    $this->previewMinLiburByLokasi = $plan['minLiburByLokasi'];
    $this->previewOffBySatpam = $plan['offBySatpamPreview'];
    $this->previewJadwalBySatpam = $plan['jadwalBySatpamPreview'];
    $this->previewGridDays = $plan['gridDays'];
    $this->previewJadwalGrid = $plan['jadwalGridPreview'];
    $this->previewPlanOffByDate = $plan['offByDate'];
    $this->previewPlanLokasiId = $this->generateLokasiId;
  }

  public function saveGenerateJadwalSatpam()
  {
    $this->authorize('jadwal-kerja.create');

    if (!$this->previewReady || $this->previewMonth !== $this->generateMonth) {
      $this->addError('generateMonth', 'Silakan buat preview terlebih dahulu.');
      return;
    }

    $inputs = $this->validateGenerateInputs();
    if (!$inputs) {
      return;
    }

    if ((int) $inputs['lokasi_id'] !== (int) $this->previewPlanLokasiId || empty($this->previewPlanOffByDate)) {
      $this->addError('generateLokasiId', 'Silakan buat preview ulang untuk lokasi ini.');
      return;
    }

    $plan = $this->buildSatpamPlan($inputs['start'], $inputs['lokasi_id']);
    if (!$plan) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data berubah sejak preview. Silakan preview ulang.'
      ]);
      return;
    }

    // Pastikan hasil save sesuai dengan preview
    $plan['offByDate'] = $this->previewPlanOffByDate;

    $result = $this->applySatpamPlan($plan);

    $this->dispatch('alert', [
      'type'    => 'success',
      'message' => "Generate jadwal selesai. Dibuat {$result['created']}, diupdate {$result['updated']}, dilewati {$result['skipped']}."
    ]);

    $this->resetGeneratePreview();
    $this->dispatch('closeGenerateModal');
  }

  protected function resetGeneratePreview(): void
  {
    $this->resetErrorBag('generateMonth');
    $this->resetErrorBag('generateLokasiId');
    $this->previewReady = false;
    $this->previewMonth = null;
    $this->previewSummary = [];
    $this->previewOffByDate = [];
    $this->previewLiburBySatpam = [];
    $this->previewMinLiburByLokasi = [];
    $this->previewOffBySatpam = [];
    $this->previewJadwalBySatpam = [];
    $this->previewGridDays = [];
    $this->previewJadwalGrid = [];
    $this->previewPlanOffByDate = [];
    $this->previewPlanLokasiId = null;
  }

  protected function defaultGenerateMonth(): string
  {
    $now = now();
    $selected = $now->day <= 7 ? $now : $now->copy()->addMonthNoOverflow();
    return $selected->format('Y-m');
  }

  protected function validateGenerateInputs(): ?array
  {
    $start = $this->validateGenerateMonth();
    if (!$start) {
      return null;
    }

    $lokasiId = $this->validateGenerateLokasi();
    if ($lokasiId === null) {
      return null;
    }

    return [
      'start' => $start,
      'lokasi_id' => $lokasiId,
    ];
  }

  protected function validateGenerateMonth(): ?Carbon
  {
    $this->resetErrorBag('generateMonth');

    if (!$this->generateMonth) {
      $this->addError('generateMonth', 'Bulan wajib dipilih.');
      return null;
    }

    try {
      $selected = Carbon::createFromFormat('Y-m', $this->generateMonth)->startOfMonth();
    } catch (\Exception $e) {
      $this->addError('generateMonth', 'Format bulan tidak valid.');
      return null;
    }

    $now = now();
    $current = $now->copy()->startOfMonth();
    $next = $now->copy()->addMonthNoOverflow()->startOfMonth();

    if ($selected->lt($current)) {
      $this->addError('generateMonth', 'Tidak boleh memilih bulan sebelum bulan ini.');
      return null;
    }

    if ($selected->eq($current) && $now->day > 7) {
      $this->addError('generateMonth', 'Generate bulan ini hanya boleh sampai minggu pertama.');
      return null;
    }

    if ($selected->gt($next)) {
      $this->addError('generateMonth', 'Hanya boleh generate bulan ini atau bulan depan.');
      return null;
    }

    return $selected;
  }

  protected function validateGenerateLokasi(): ?int
  {
    $this->resetErrorBag('generateLokasiId');

    if (!$this->generateLokasiId) {
      $this->addError('generateLokasiId', 'Lokasi wajib dipilih.');
      return null;
    }

    $lokasiId = (int) $this->generateLokasiId;
    if (!Lokasi::whereKey($lokasiId)->exists()) {
      $this->addError('generateLokasiId', 'Lokasi tidak valid.');
      return null;
    }

    return $lokasiId;
  }

  protected function buildSatpamPlan(Carbon $start, ?int $lokasiId = null): ?array
  {
    $end = $start->copy()->endOfMonth();

    $maxLiburBeruntun = (int) config('jadwal.max_libur_beruntun', 2);

    $shiftRotation = $this->resolveShiftRotation();
    if (count($shiftRotation) < 3) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Shift satpam belum lengkap (pagi, sore, malam).'
      ]);
      return null;
    }

    $satpams = Karyawan::onlyAktif()
      ->whereHas('jabatan', function ($q) {
        $q->where('nama', 'like', '%satpam%');
      })
      ->when($lokasiId, function ($q) use ($lokasiId) {
        $q->where('lokasi_id', $lokasiId);
      })
      ->with([
        'jabatan:id,nama',
        'lokasi:id,nama',
        'workRule.days',
        'jadwalKaryawans' => function ($q) use ($start, $end) {
          $q->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        },
      ])
      ->get();

    if ($satpams->isEmpty()) {
      $lokasiName = $lokasiId ? (Lokasi::find($lokasiId)?->nama ?? 'Lokasi') : 'lokasi';
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => "Tidak ada karyawan satpam aktif di {$lokasiName}."
      ]);
      return null;
    }

    foreach ($satpams as $satpam) {
      if (!$satpam->workRule || !$satpam->workRule->use_shift) {
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => "Work rule satpam {$satpam->nama} belum lengkap atau tidak memakai shift."
        ]);
        return null;
      }
    }

    $dates = [];
    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
      $dates[] = $date->toDateString();
    }

    $holidaySet = Holiday::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
      ->pluck('tanggal')
      ->map(fn($d) => $d->toDateString())
      ->flip()
      ->all();

    $offByDate = [];
    $offByKaryawan = [];
    $satpamById = [];
    $lokasiById = [];
    $minLiburByLokasi = [];

    foreach ($satpams as $satpam) {
      $offByKaryawan[$satpam->id] = [];
      $satpamById[$satpam->id] = $satpam;
      $lokasiKey = $satpam->lokasi_id ?? 0;
      if (!isset($lokasiById[$lokasiKey])) {
        $lokasiById[$lokasiKey] = $satpam->lokasi?->nama ?? 'Tanpa Lokasi';
      }
      $offByDate[$lokasiKey] = $offByDate[$lokasiKey] ?? [];
    }

    $satpamsByLokasi = $satpams->groupBy(fn($s) => $s->lokasi_id ?? 0);

    foreach ($satpamsByLokasi as $lokasiKey => $group) {
      foreach ($group as $satpam) {
        $ruleDays = $satpam->workRule?->days?->keyBy('day_of_week') ?? collect();

        if (!$satpam->workRule?->use_shift) {
          foreach ($dates as $dateStr) {
            $dayOfWeek = Carbon::parse($dateStr)->dayOfWeekIso;
            $ruleDay = $ruleDays->get($dayOfWeek);

            if ($ruleDay && !$ruleDay->is_workday) {
              if (isset($offByDate[$lokasiKey][$dateStr]) && $offByDate[$lokasiKey][$dateStr] !== $satpam->id) {
                $lokasiLabel = $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi';
                $this->dispatch('alert', [
                  'type'    => 'error',
                  'message' => "Terdeteksi lebih dari satu satpam libur di {$dateStr} ({$lokasiLabel})."
                ]);
                return null;
              }

              $offByDate[$lokasiKey][$dateStr] = $satpam->id;
              if (!in_array($dateStr, $offByKaryawan[$satpam->id], true)) {
                $offByKaryawan[$satpam->id][] = $dateStr;
              }
            }
          }
        }

        foreach ($satpam->jadwalKaryawans as $jadwal) {
          if (!$jadwal->is_libur) {
            continue;
          }

          $generatedBy = $jadwal->generated_by ? strtolower((string) $jadwal->generated_by) : null;
          if ($generatedBy && $generatedBy !== 'manual') {
            continue; // libur hasil generate sebelumnya boleh di-redistribute
          }

          $dateStr = $jadwal->tanggal?->toDateString();
          if (!$dateStr) {
            continue;
          }

          if (isset($offByDate[$lokasiKey][$dateStr]) && $offByDate[$lokasiKey][$dateStr] !== $satpam->id) {
            $lokasiLabel = $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi';
            $this->dispatch('alert', [
              'type'    => 'error',
              'message' => "Terdeteksi lebih dari satu satpam libur di {$dateStr} ({$lokasiLabel})."
            ]);
            return null;
          }

          $offByDate[$lokasiKey][$dateStr] = $satpam->id;
          if (!in_array($dateStr, $offByKaryawan[$satpam->id], true)) {
            $offByKaryawan[$satpam->id][] = $dateStr;
          }
        }
      }
    }

    foreach ($satpams as $satpam) {
      if ($this->maxConsecutiveOff($offByKaryawan[$satpam->id]) > $maxLiburBeruntun) {
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => "Libur satpam {$satpam->nama} melebihi batas {$maxLiburBeruntun} hari berturut-turut."
        ]);
        return null;
      }
    }

    $neededByKaryawan = [];

    foreach ($satpamsByLokasi as $lokasiKey => $group) {
      $minLiburForLokasi = $this->resolveMinLiburForLokasi($start, $group->count());
      $minLiburByLokasi[$lokasiKey] = [
        'lokasi' => $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi',
        'min' => $minLiburForLokasi,
        'jumlah_satpam' => $group->count(),
      ];

      $totalNeeded = 0;
      foreach ($group as $satpam) {
        $currentOff = count($offByKaryawan[$satpam->id]);
        $need = max(0, $minLiburForLokasi - $currentOff);
        $neededByKaryawan[$satpam->id] = $need;
        $totalNeeded += $need;
      }

      $availableDates = array_values(array_diff($dates, array_keys($offByDate[$lokasiKey] ?? [])));
      if ($totalNeeded > count($availableDates)) {
        $lokasiLabel = $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi';
        $detailParts = [];
        foreach ($group as $satpam) {
          $detailParts[] = "{$satpam->nama}: butuh {$neededByKaryawan[$satpam->id]} (libur saat ini " . count($offByKaryawan[$satpam->id]) . ")";
        }
        $detailInfo = implode(', ', $detailParts);
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => "Tidak cukup hari kosong untuk memenuhi kuota libur minimal satpam di {$lokasiLabel}. Kuota per orang {$minLiburForLokasi}, hari kosong " . count($availableDates) . ", kebutuhan {$totalNeeded}. Detail: {$detailInfo}."
        ]);
        return null;
      }

      $progress = true;
      while ($totalNeeded > 0 && $progress) {
        $progress = false;
        foreach ($group as $satpam) {
          if ($neededByKaryawan[$satpam->id] <= 0) {
            continue;
          }

          foreach ($availableDates as $index => $candidate) {
            if ($this->wouldExceedConsecutive($offByKaryawan[$satpam->id], $candidate, $maxLiburBeruntun)) {
              continue;
            }

            $offByDate[$lokasiKey][$candidate] = $satpam->id;
            $offByKaryawan[$satpam->id][] = $candidate;
            unset($availableDates[$index]);
            $availableDates = array_values($availableDates);
            $neededByKaryawan[$satpam->id]--;
            $totalNeeded--;
            $progress = true;
            break;
          }
        }
      }

      if ($totalNeeded > 0) {
        $lokasiLabel = $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi';
        $detailParts = [];
        foreach ($group as $satpam) {
          $detailParts[] = "{$satpam->nama}: butuh {$neededByKaryawan[$satpam->id]}";
        }
        $detailInfo = implode(', ', $detailParts);
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => "Gagal menyusun libur sesuai aturan di {$lokasiLabel}. Kuota per orang {$minLiburForLokasi}, sisa kebutuhan {$totalNeeded}. Detail: {$detailInfo}. Coba ubah konfigurasi atau periksa jadwal manual."
        ]);
        return null;
      }
    }

    $created = 0;
    $updated = 0;
    $skipped = 0;

    foreach ($satpams as $satpam) {
      $existingByDate = $satpam->jadwalKaryawans
        ->keyBy(fn($jadwal) => $jadwal->tanggal?->toDateString());

      foreach ($dates as $dateStr) {
        $existing = $existingByDate->get($dateStr);
        if ($existing && ($existing->generated_by === 'manual' || $existing->generated_by === null)) {
          $skipped++;
          continue;
        }

        if ($existing) {
          $updated++;
        } else {
          $created++;
        }
      }
    }

    $offByDatePreview = [];
    foreach ($offByDate as $lokasiKey => $dateMap) {
      foreach ($dateMap as $dateStr => $satpamId) {
        $satpam = $satpamById[$satpamId] ?? null;
        $offByDatePreview[] = [
          'tanggal' => Carbon::parse($dateStr)->format('d-m-Y'),
          'lokasi'  => $lokasiById[$lokasiKey] ?? 'Tanpa Lokasi',
          'satpam'  => $satpam?->nama ?? '-',
        ];
      }
    }

    $offBySatpamPreview = [];
    foreach ($satpams as $satpam) {
      $offDates = array_map(
        fn($d) => Carbon::parse($d)->format('d-m-Y'),
        $offByKaryawan[$satpam->id] ?? []
      );
      sort($offDates);
      $offBySatpamPreview[] = [
        'lokasi' => $satpam->lokasi?->nama ?? 'Tanpa Lokasi',
        'satpam' => $satpam->nama,
        'dates'  => $offDates,
      ];
    }

    $jadwalBySatpamPreview = [];
    $jadwalGridPreview = [];
    foreach ($satpams as $satpam) {
      $ruleDays = $satpam->workRule?->days?->keyBy('day_of_week') ?? collect();
      $offset = $satpam->id % max(1, count($shiftRotation));
      $lokasiKey = $satpam->lokasi_id ?? 0;
      $offByDateLokasi = $offByDate[$lokasiKey] ?? [];
      $items = [];
      $grid = [];

      for ($day = 1; $day <= $start->daysInMonth; $day++) {
        $date = $start->copy()->addDays($day - 1);
        $dateStr = $date->toDateString();
        $dayOfWeek = $date->dayOfWeekIso;
        $ruleDay = $ruleDays->get($dayOfWeek);
        $isWorkday = $satpam->workRule?->use_shift ? true : ($ruleDay?->is_workday ?? true);

        $isLibur = !$isWorkday;
        if (isset($offByDateLokasi[$dateStr]) && $offByDateLokasi[$dateStr] === $satpam->id) {
          $isLibur = true;
        }

        if ($isLibur) {
          $code = 'L';
        } elseif ($satpam->workRule?->use_shift) {
          $dayIndex = $start->diffInDays($date);
          $shift = $shiftRotation[($offset + $dayIndex) % count($shiftRotation)];
          $code = strtoupper(substr((string) $shift->nama, 0, 1));
        } else {
          $code = 'W';
        }

        $items[] = $date->format('d') . ':' . $code;
        $grid[$day] = $code;
      }

      $jadwalBySatpamPreview[] = [
        'lokasi' => $satpam->lokasi?->nama ?? 'Tanpa Lokasi',
        'satpam' => $satpam->nama,
        'items'  => $items,
      ];

      $jadwalGridPreview[] = [
        'lokasi' => $satpam->lokasi?->nama ?? 'Tanpa Lokasi',
        'satpam' => $satpam->nama,
        'grid'   => $grid,
      ];
    }

    $gridDays = range(1, $start->daysInMonth);

    $liburBySatpam = [];
    foreach ($satpams as $satpam) {
      $liburBySatpam[] = [
        'lokasi' => $satpam->lokasi?->nama ?? 'Tanpa Lokasi',
        'satpam' => $satpam->nama,
        'total'  => count($offByKaryawan[$satpam->id]),
      ];
    }

    return [
      'start'            => $start,
      'dates'            => $dates,
      'satpams'          => $satpams,
      'holidaySet'       => $holidaySet,
      'offByDate'        => $offByDate,
      'shiftRotation'    => $shiftRotation,
      'minLiburByLokasi' => array_values($minLiburByLokasi),
      'summary'          => [
        'created' => $created,
        'updated' => $updated,
        'skipped' => $skipped,
      ],
      'offByDatePreview' => $offByDatePreview,
      'offBySatpamPreview' => $offBySatpamPreview,
      'jadwalBySatpamPreview' => $jadwalBySatpamPreview,
      'jadwalGridPreview' => $jadwalGridPreview,
      'gridDays' => $gridDays,
      'liburBySatpam'    => $liburBySatpam,
    ];
  }

  protected function applySatpamPlan(array $plan): array
  {
    $created = 0;
    $updated = 0;
    $skipped = 0;

    $satpams = $plan['satpams'];
    $dates = $plan['dates'];
    $holidaySet = $plan['holidaySet'];
    $offByDate = $plan['offByDate'];
    $shiftRotation = $plan['shiftRotation'];
    $start = $plan['start'];

    DB::transaction(function () use (
      $satpams,
      $dates,
      $holidaySet,
      $offByDate,
      $shiftRotation,
      $start,
      &$created,
      &$updated,
      &$skipped
    ) {
      foreach ($satpams as $satpam) {
        $existingByDate = $satpam->jadwalKaryawans
          ->keyBy(fn($jadwal) => $jadwal->tanggal?->toDateString());

        $ruleDays = $satpam->workRule?->days?->keyBy('day_of_week') ?? collect();
        $offset = $satpam->id % max(1, count($shiftRotation));

        $lokasiKey = $satpam->lokasi_id ?? 0;
        $offByDateLokasi = $offByDate[$lokasiKey] ?? [];

        foreach ($dates as $dateStr) {
          $existing = $existingByDate->get($dateStr);
          if ($existing && ($existing->generated_by === 'manual' || $existing->generated_by === null)) {
            $skipped++;
            continue;
          }

          $date = Carbon::parse($dateStr);
          $dayOfWeek = $date->dayOfWeekIso;
          $ruleDay = $ruleDays->get($dayOfWeek);
          $isWorkday = $satpam->workRule?->use_shift ? true : ($ruleDay?->is_workday ?? true);

          $isLibur = !$isWorkday;
          if (isset($offByDateLokasi[$dateStr]) && $offByDateLokasi[$dateStr] === $satpam->id) {
            $isLibur = true;
          }

          $shiftId = null;
          $shiftNama = null;
          $jamMasuk = null;
          $jamPulang = null;

          if (!$isLibur) {
            if ($satpam->workRule?->use_shift) {
              $dayIndex = $start->diffInDays($date);
              $shift = $shiftRotation[($offset + $dayIndex) % count($shiftRotation)];
              $shiftId = $shift->id;
              $shiftNama = $shift->nama;
              $jamMasuk = $shift->jam_masuk?->format('H:i');
              $jamPulang = $shift->jam_pulang?->format('H:i');
            } else {
              $shiftNama = null;
              $jamMasuk = $ruleDay?->jam_masuk?->format('H:i');
              $jamPulang = $ruleDay?->jam_pulang?->format('H:i');
            }
          }

          $payload = [
            'karyawan_id' => $satpam->id,
            'tanggal'     => $dateStr,
            'shift_id'    => $shiftId,
            'shift_nama'  => $shiftNama,
            'jam_masuk'   => $jamMasuk,
            'jam_pulang'  => $jamPulang,
            'is_libur'    => (bool) $isLibur,
            'is_holiday'  => isset($holidaySet[$dateStr]),
            'generated_by' => 'system',
          ];

          if ($existing) {
            $existing->update(array_merge($payload, [
              'updated_by' => auth()->id(),
            ]));
            $updated++;
          } else {
            JadwalKaryawanModel::create(array_merge($payload, [
              'created_by' => auth()->id(),
              'updated_by' => auth()->id(),
            ]));
            $created++;
          }
        }
      }
    });

    return [
      'created' => $created,
      'updated' => $updated,
      'skipped' => $skipped,
    ];
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

  protected function loadLokasis(): void
  {
    if (!empty($this->lokasis)) {
      return;
    }

    $this->lokasis = Lokasi::query()
      ->select('id', 'nama')
      ->orderBy('nama')
      ->get();
  }

  protected function lokasiOptions(): array
  {
    return collect($this->lokasis)
      ->map(fn($lokasi) => [
        'id'   => $lokasi->id,
        'nama' => $lokasi->nama,
      ])
      ->values()
      ->all();
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

  protected function resolveShiftRotation(): array
  {
    $rotation = config('jadwal.shift_rotation', ['pagi', 'sore', 'malam']);
    $rotation = array_values(array_filter(array_map(function ($item) {
      return $item ? strtolower(trim((string) $item)) : null;
    }, $rotation)));

    $shifts = ShiftMaster::query()
      ->where('is_active', true)
      ->get();

    $map = [];
    foreach ($shifts as $shift) {
      $map[strtolower((string) $shift->nama)] = $shift;
    }

    $result = [];
    foreach ($rotation as $name) {
      if (!isset($map[$name])) {
        return [];
      }
      $result[] = $map[$name];
    }

    return $result;
  }

  protected function resolveMinLiburForLokasi(Carbon $start, int $satpamCount): int
  {
    $daysInMonth = $start->daysInMonth;
    $weeksInMonth = (int) ceil($daysInMonth / 7);
    $weeksInMonth = max(3, min(4, $weeksInMonth));

    $capacity = intdiv($daysInMonth, max(1, $satpamCount));

    return max(0, min($weeksInMonth, $capacity));
  }

  protected function maxConsecutiveOff(array $offDates): int
  {
    if (empty($offDates)) {
      return 0;
    }

    $set = array_flip($offDates);
    $max = 1;

    foreach ($offDates as $dateStr) {
      $count = 1;
      $date = Carbon::parse($dateStr);
      $prev = $date->copy()->subDay();
      while (isset($set[$prev->toDateString()])) {
        $count++;
        $prev->subDay();
      }
      $next = $date->copy()->addDay();
      while (isset($set[$next->toDateString()])) {
        $count++;
        $next->addDay();
      }
      $max = max($max, $count);
    }

    return $max;
  }

  protected function wouldExceedConsecutive(array $offDates, string $candidate, int $maxConsecutive): bool
  {
    if ($maxConsecutive <= 0) {
      return false;
    }

    $set = array_flip($offDates);
    $set[$candidate] = true;

    $date = Carbon::parse($candidate);
    $count = 1;
    $prev = $date->copy()->subDay();
    while (isset($set[$prev->toDateString()])) {
      $count++;
      $prev->subDay();
    }
    $next = $date->copy()->addDay();
    while (isset($set[$next->toDateString()])) {
      $count++;
      $next->addDay();
    }

    return $count > $maxConsecutive;
  }

  protected function validateSatpamLiburRules(): bool
  {
    if (!$this->karyawanId || !$this->tanggal) {
      return true;
    }

    $karyawan = Karyawan::with(['jabatan:id,nama', 'workRule', 'lokasi:id,nama'])
      ->find($this->karyawanId);

    if (!$karyawan || !$karyawan->workRule) {
      return true;
    }

    $jabatanName = strtolower((string) ($karyawan->jabatan?->nama ?? ''));
    if (!$karyawan->workRule->use_shift || !str_contains($jabatanName, 'satpam')) {
      return true;
    }

    $date = Carbon::parse($this->tanggal);
    $start = $date->copy()->startOfMonth();
    $end = $date->copy()->endOfMonth();

    $maxLiburBeruntun = (int) config('jadwal.max_libur_beruntun', 2);
    $lokasiId = $karyawan->lokasi_id;
    $satpamCountQuery = Karyawan::onlyAktif()
      ->whereHas('jabatan', function ($q) {
        $q->where('nama', 'like', '%satpam%');
      });

    if ($lokasiId) {
      $satpamCountQuery->where('lokasi_id', $lokasiId);
    } else {
      $satpamCountQuery->whereNull('lokasi_id');
    }

    $minLibur = $this->resolveMinLiburForLokasi($start, $satpamCountQuery->count());

    if ((bool) $this->isLibur) {
      $conflict = JadwalKaryawanModel::whereDate('tanggal', $date->toDateString())
        ->where('is_libur', true)
        ->where('karyawan_id', '!=', $this->karyawanId)
        ->whereHas('karyawan', function ($q) use ($lokasiId) {
          $q->whereHas('jabatan', function ($jabatanQuery) {
            $jabatanQuery->where('nama', 'like', '%satpam%');
          });

          if ($lokasiId) {
            $q->where('lokasi_id', $lokasiId);
          } else {
            $q->whereNull('lokasi_id');
          }
        })
        ->exists();

      if ($conflict) {
        $lokasiLabel = $karyawan->lokasi?->nama ?? 'Tanpa Lokasi';
        $this->addError('isLibur', "Tidak boleh ada 2 satpam libur di hari yang sama untuk lokasi {$lokasiLabel}.");
        return false;
      }
    }

    $existingLiburDates = JadwalKaryawanModel::where('karyawan_id', $this->karyawanId)
      ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
      ->where('is_libur', true)
      ->when($this->jadwalId, function ($q) {
        $q->where('id', '!=', $this->jadwalId);
      })
      ->pluck('tanggal')
      ->map(fn($d) => $d->toDateString())
      ->all();

    if ((bool) $this->isLibur) {
      if ($this->wouldExceedConsecutive($existingLiburDates, $date->toDateString(), $maxLiburBeruntun)) {
        $this->addError('isLibur', "Libur tidak boleh lebih dari {$maxLiburBeruntun} hari berturut-turut.");
        return false;
      }
    }

    $currentIsLibur = false;
    if ($this->jadwalId) {
      $currentIsLibur = (bool) JadwalKaryawanModel::where('id', $this->jadwalId)
        ->value('is_libur');
    }

    if ($currentIsLibur && !(bool) $this->isLibur) {
      if (count($existingLiburDates) < $minLibur) {
        $this->addError('isLibur', "Minimal libur per bulan adalah {$minLibur} hari.");
        return false;
      }
    }

    return true;
  }
}
