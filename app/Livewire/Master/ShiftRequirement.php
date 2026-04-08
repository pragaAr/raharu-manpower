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
  ShiftRequirement as ShiftRequirementModel,
  Lokasi,
  ShiftMaster
};

#[Title('Shift Requirement')]
class ShiftRequirement extends Component
{
  use AuthorizesRequests, WithPagination;

  public $shiftRequirementId;
  public $lokasi_id;
  public $shift_id;
  public $required_count = 0;
  public $isEdit   = false;
  public $deleteId = null;

  public $lokasis = [];
  public $shifts = [];

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
    $this->loadLokasis();
    $this->loadShifts();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  public function render()
  {
    $term = trim((string) $this->search);

    $query = ShiftRequirementModel::with(['lokasi', 'shift'])
      ->when($term !== '', function ($q) use ($term) {
        $q->where(function ($sub) use ($term) {
          $sub->where('required_count', 'like', "%{$term}%")
            ->orWhereHas('lokasi', function ($lokasiQuery) use ($term) {
              $lokasiQuery->where('nama', 'like', "%{$term}%");
            })
            ->orWhereHas('shift', function ($shiftQuery) use ($term) {
              $shiftQuery->where('nama', 'like', "%{$term}%");
            });
        });
      })
      ->orderBy('lokasi_id', 'ASC')
      ->orderBy('shift_id', 'ASC');

    return view('livewire.master.work_config.shift_requirements.index', [
      'data'       => $query->paginate(10),
      'title'      => 'Shift Requirement',
      'hasActions' => auth()->user()->canAny([
        'shift-requirement.edit',
        'shift-requirement.delete',
        'shift-requirement.create',
      ]),
    ]);
  }

  public function create()
  {
    $this->authorize('shift-requirement.create');

    $this->resetValidation();
    $this->resetForm();
    $this->isEdit = false;

    $this->dispatch('openModal');
  }

  public function edit($id)
  {
    $this->authorize('shift-requirement.edit');

    $this->resetValidation();
    $item = ShiftRequirementModel::find($id);

    if (!$item) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
      return;
    }

    $this->shiftRequirementId = $id;
    $this->lokasi_id = $item->lokasi_id;
    $this->shift_id = $item->shift_id;
    $this->required_count = (int) $item->required_count;
    $this->isEdit = true;

    $this->dispatch('openModal', lokasi_id: $this->lokasi_id, shift_id: $this->shift_id);
  }

  public function save()
  {
    $this->validate(
      [
        'lokasi_id' => ['required', 'exists:lokasi,id'],
        'shift_id' => [
          'required',
          'exists:shift_master,id',
          Rule::unique('shift_requirement', 'shift_id')
            ->where(fn($q) => $q->where('lokasi_id', $this->lokasi_id))
            ->ignore($this->shiftRequirementId),
        ],
        'required_count' => ['required', 'integer', 'min:0'],
      ],
      [
        'lokasi_id.required' => 'Lokasi wajib dipilih.',
        'lokasi_id.exists' => 'Lokasi tidak valid.',
        'shift_id.required' => 'Shift wajib dipilih.',
        'shift_id.exists' => 'Shift tidak valid.',
        'shift_id.unique' => 'Kombinasi lokasi dan shift sudah ada.',
        'required_count.required' => 'Kebutuhan wajib diisi.',
        'required_count.integer' => 'Kebutuhan harus berupa angka.',
        'required_count.min' => 'Kebutuhan minimal 0.',
      ]
    );

    $this->isEdit ? $this->updateData() : $this->storeData();

    $this->resetForm();
    $this->dispatch('closeModal');
  }

  public function storeData()
  {
    $this->authorize('shift-requirement.create');

    try {
      ShiftRequirementModel::create([
        'lokasi_id' => $this->lokasi_id,
        'shift_id' => $this->shift_id,
        'required_count' => (int) $this->required_count,
      ]);

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
    $this->authorize('shift-requirement.edit');

    try {
      ShiftRequirementModel::find($this->shiftRequirementId)?->update([
        'lokasi_id' => $this->lokasi_id,
        'shift_id' => $this->shift_id,
        'required_count' => (int) $this->required_count,
      ]);

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
    $this->authorize('shift-requirement.delete');

    $this->deleteId = $id;
    $this->dispatch('openConfirmModal');
  }

  public function deleteData()
  {
    $this->authorize('shift-requirement.delete');

    if ($this->deleteId) {
      try {
        ShiftRequirementModel::destroy($this->deleteId);
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

  protected function loadLokasis(): void
  {
    $this->lokasis = Lokasi::query()
      ->select('id', 'nama')
      ->orderBy('nama')
      ->get();
  }

  protected function loadShifts(): void
  {
    $this->shifts = ShiftMaster::query()
      ->select('id', 'nama', 'is_active')
      ->orderByDesc('is_active')
      ->orderBy('nama')
      ->get();
  }

  private function resetForm()
  {
    $this->reset([
      'shiftRequirementId',
      'lokasi_id',
      'shift_id',
      'required_count',
    ]);

    $this->required_count = 0;
  }
}
