<?php

namespace App\Livewire\Master;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\{
  Component,
  WithPagination
};
use Livewire\Attributes\Title;

use App\Models\{
  WorkRule as WorkRuleModel,
  WorkRuleDay,
  Jabatan
};

#[Title('Aturan Kerja')]
class WorkRule extends Component
{
  use AuthorizesRequests, WithPagination;

  public $workRuleId;
  public $jabatan_id;
  public $use_shift = false;
  public $auto_overtime = false;
  public $overtime_need_approval = true;
  public $cuti_need_approval = true;
  public $allow_double_shift = false;
  public $allow_shift_swap = false;
  public $isEdit    = false;
  public $deleteId  = null;

  public $jabatans = [];
  public $days = [];
  public $dayLabels = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
  ];

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
    $this->jabatans = Jabatan::join('unit', 'jabatan.unit_id', '=', 'unit.id')
      ->join('divisi', 'unit.divisi_id', '=', 'divisi.id')
      ->orderBy('divisi.nama')
      ->orderBy('unit.nama')
      ->orderBy('jabatan.nama')
      ->get([
        'jabatan.id',
        'jabatan.nama',
        'unit.nama as unit_nama',
        'divisi.nama as divisi_nama',
      ]);

    $this->initializeDays();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = WorkRuleModel::with(['jabatan.unit.divisi', 'days'])
      ->when($term !== '', function ($q) use ($term) {
        $like = "{$term}%";

        $q->whereHas('jabatan', function ($jabatanQuery) use ($like) {
          $jabatanQuery->where('nama', 'like', $like)
            ->orWhereHas('unit', function ($unitQuery) use ($like) {
              $unitQuery->where('nama', 'like', $like)
                ->orWhereHas('divisi', function ($divisiQuery) use ($like) {
                  $divisiQuery->where('nama', 'like', $like);
                });
            });
        });
      })
      ->orderBy('id', 'ASC');

    return view('livewire.master.work_config.work_rules.index', [
      'data'       => $query->paginate(10),
      'title'      => 'Aturan Kerja',
      'hasActions' => auth()->user()->canAny([
        'work-rule.edit',
        'work-rule.delete',
        'work-rule.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('work-rule.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal', jabatan_id: $this->jabatan_id);
  }

  public function edit($id)
  {
    $this->authorize('work-rule.edit');

    $this->resetValidation();
    $workRule = WorkRuleModel::with('days')->find($id);

    if (!$workRule) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->workRuleId              = $id;
    $this->jabatan_id              = $workRule->jabatan_id;
    $this->use_shift               = (bool) $workRule->use_shift;
    $this->auto_overtime           = (bool) $workRule->auto_overtime;
    $this->overtime_need_approval  = (bool) $workRule->overtime_need_approval;
    $this->cuti_need_approval      = (bool) $workRule->cuti_need_approval;
    $this->allow_double_shift      = (bool) $workRule->allow_double_shift;
    $this->allow_shift_swap        = (bool) $workRule->allow_shift_swap;
    $this->isEdit                  = true;

    $existingDays = [];
    foreach ($workRule->days as $day) {
      $existingDays[(int) $day->day_of_week] = [
        'is_workday' => (bool) $day->is_workday,
        'jam_masuk'  => $day->jam_masuk?->format('H:i'),
        'jam_pulang' => $day->jam_pulang?->format('H:i'),
      ];
    }
    $this->initializeDays($existingDays);

    $this->dispatch('openModal', jabatan_id: $this->jabatan_id);
  }

  public function save()
  {
    $this->validate(
      [
        'jabatan_id'             => ['required', Rule::exists('jabatan', 'id'), Rule::unique('work_rule', 'jabatan_id')->ignore($this->workRuleId)],
        'use_shift'              => ['boolean'],
        'auto_overtime'          => ['boolean'],
        'overtime_need_approval' => ['boolean'],
        'cuti_need_approval'     => ['boolean'],
        'allow_double_shift'     => ['boolean'],
        'allow_shift_swap'       => ['boolean'],
        'days'                   => ['array'],
        'days.*.is_workday'      => ['boolean'],
        'days.*.jam_masuk'       => ['nullable', 'date_format:H:i'],
        'days.*.jam_pulang'      => ['nullable', 'date_format:H:i'],
      ],
      [
        'jabatan_id.required'      => 'Jabatan wajib dipilih.',
        'jabatan_id.exists'        => 'Jabatan tidak valid.',
        'jabatan_id.unique'        => 'Aturan kerja untuk jabatan ini sudah ada.',
        'days.*.jam_masuk.date_format' => 'Format jam masuk tidak valid (HH:MM).',
        'days.*.jam_pulang.date_format' => 'Format jam pulang tidak valid (HH:MM).',
      ]
    );

    if (!$this->validateWorkDays()) {
      return;
    }

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('work-rule.create');

    try {
      DB::transaction(function () {
        $workRule = WorkRuleModel::create([
          'jabatan_id'              => $this->jabatan_id,
          'use_shift'               => (bool) $this->use_shift,
          'auto_overtime'           => (bool) $this->auto_overtime,
          'overtime_need_approval'  => (bool) $this->overtime_need_approval,
          'cuti_need_approval'      => (bool) $this->cuti_need_approval,
          'allow_double_shift'      => (bool) $this->allow_double_shift,
          'allow_shift_swap'        => (bool) $this->allow_shift_swap,
        ]);

        $this->syncDays($workRule->id);
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal ditambah.'
      ]);
    }
  }

  public function updateData()
  {
    $this->authorize('work-rule.edit');

    try {
      DB::transaction(function () {
        WorkRuleModel::find($this->workRuleId)->update([
          'jabatan_id'              => $this->jabatan_id,
          'use_shift'               => (bool) $this->use_shift,
          'auto_overtime'           => (bool) $this->auto_overtime,
          'overtime_need_approval'  => (bool) $this->overtime_need_approval,
          'cuti_need_approval'      => (bool) $this->cuti_need_approval,
          'allow_double_shift'      => (bool) $this->allow_double_shift,
          'allow_shift_swap'        => (bool) $this->allow_shift_swap,
        ]);

        $this->syncDays($this->workRuleId);
      });

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil diupdate.'
      ]);
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data gagal diupdate.'
      ]);
    }
  }

  public function confirmDelete($id)
  {
    $this->authorize('work-rule.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('work-rule.delete');

    if ($this->deleteId) {
      try {
        WorkRuleModel::destroy($this->deleteId);
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

  private function syncDays(int $workRuleId): void
  {
    foreach (array_keys($this->dayLabels) as $dayNumber) {
      $isWorkday = (bool) data_get($this->days, "{$dayNumber}.is_workday", false);
      $jamMasuk  = data_get($this->days, "{$dayNumber}.jam_masuk");
      $jamPulang = data_get($this->days, "{$dayNumber}.jam_pulang");

      if ($this->use_shift || !$isWorkday) {
        $jamMasuk = null;
        $jamPulang = null;
      }

      WorkRuleDay::updateOrCreate(
        [
          'work_rule_id' => $workRuleId,
          'day_of_week'  => $dayNumber,
        ],
        [
          'is_workday' => $isWorkday,
          'jam_masuk'  => $jamMasuk ?: null,
          'jam_pulang' => $jamPulang ?: null,
        ]
      );
    }
  }

  private function validateWorkDays(): bool
  {
    if ($this->use_shift) {
      return true;
    }

    foreach (array_keys($this->dayLabels) as $dayNumber) {
      $isWorkday = (bool) data_get($this->days, "{$dayNumber}.is_workday", false);
      $jamMasuk  = data_get($this->days, "{$dayNumber}.jam_masuk");
      $jamPulang = data_get($this->days, "{$dayNumber}.jam_pulang");

      if ($isWorkday && (!$jamMasuk || !$jamPulang)) {
        $this->addError("days.$dayNumber.jam_masuk", 'Jam masuk dan jam pulang wajib diisi untuk hari kerja.');
        return false;
      }
    }

    return true;
  }

  private function initializeDays(array $existingDays = []): void
  {
    $defaults = [
      1 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '16:00'],
      2 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '16:00'],
      3 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '16:00'],
      4 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '16:00'],
      5 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '16:00'],
      6 => ['is_workday' => true,  'jam_masuk' => '08:00', 'jam_pulang' => '14:00'],
      7 => ['is_workday' => false, 'jam_masuk' => null,    'jam_pulang' => null],
    ];

    foreach ($defaults as $dayNumber => $defaultDay) {
      $this->days[$dayNumber] = array_merge($defaultDay, $existingDays[$dayNumber] ?? []);
    }
  }

  private function resetForm()
  {
    $this->reset([
      'workRuleId',
      'jabatan_id',
    ]);

    $this->use_shift = false;
    $this->auto_overtime = false;
    $this->overtime_need_approval = true;
    $this->cuti_need_approval = true;
    $this->allow_double_shift = false;
    $this->allow_shift_swap = false;

    $this->initializeDays();
  }
}
