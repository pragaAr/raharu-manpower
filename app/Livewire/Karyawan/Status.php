<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\{
  Karyawan,
  Lokasi,
  History
};

use App\Services\Karyawan\UpdateStatus;

class Status extends Component
{
  use AuthorizesRequests;

  public $initial = [];
  public bool $isInitializing = false;

  public $karyawanId;
  public $status, $statusAkhir;
  public $tglEfektif, $tglKeluar, $tglMulai, $tglSelesai;
  public $keterangan;

  public $lokasiId;

  public $lokasis = [];

  public function mount()
  {
    $this->lokasis = Lokasi::orderBy('nama')->get(['id', 'nama']);
  }

  public function updatedStatus($value)
  {
    if ($this->isInitializing) return;

    if ($value === $this->statusAkhir) {
      $this->keterangan = $this->initial['keterangan'] ?? null;
      $this->tglMulai   = $this->initial['tglMulai']   ?? null;
      $this->tglSelesai = $this->initial['tglSelesai'] ?? null;
      $this->tglKeluar  = $this->initial['tglKeluar']  ?? null;
      $this->tglEfektif = $this->initial['tglEfektif'] ?? null;
    } else {
      $this->reset([
        'tglMulai',
        'tglSelesai',
        'tglKeluar',
        'tglEfektif',
        'lokasiId',
        'keterangan',
      ]);
    }
  }

  #[On('open-update-status')]
  public function open($id)
  {
    $this->authorize('karyawan.update-status');

    $this->isInitializing = true;

    $this->resetValidation();

    $karyawan = Karyawan::select('id', 'nama', 'lokasi_id', 'status')
      ->findOrFail($id);

    $lastHistory = History::where('karyawan_id', $id)
      ->orderByDesc('created_at')
      ->first();

    $isAktif = $karyawan->status === 'aktif' ? true : false;

    $this->initial = [
      'keterangan' => !$isAktif ? $lastHistory?->keterangan : null,
      'tglMulai'   => $lastHistory?->tgl_mulai?->toDateString(),
      'tglSelesai' => $lastHistory?->tgl_selesai?->toDateString(),
      'tglKeluar'  => $lastHistory?->tgl_keluar?->toDateString(),
      'tglEfektif' => $lastHistory?->tgl_efektif?->toDateString(),
    ];

    $this->karyawanId   = $id;
    $this->status       = $karyawan->status;
    $this->statusAkhir  = $karyawan->status;

    $this->keterangan = $this->initial['keterangan'];
    $this->tglMulai   = $this->initial['tglMulai'];
    $this->tglSelesai = $this->initial['tglSelesai'];
    $this->tglKeluar  = $this->initial['tglKeluar'];
    $this->tglEfektif = $this->initial['tglEfektif'];

    $this->lokasiId   = $karyawan->lokasi_id;

    $this->isInitializing = false;

    $this->dispatch('openStatus');
  }

  public function updateStatus()
  {
    $this->authorize('karyawan.change_status');

    $this->validate(
      [
        'status'       => ['required'],
        'tglMulai'     => ['required_if:status,vakum'],
        'tglSelesai'   => ['required_if:status,vakum'],
        'tglKeluar'    => ['required_if:status,nonaktif'],
        'tglEfektif'   => ['required_if:status,aktif',],
        'keterangan'   => ['required'],
      ],
      [
        'status.required'         => 'Status wajib diisi.',
        'tglMulai.required_if'    => 'Tanggal mulai wajib diisi.',
        'tglSelesai.required_if'  => 'Tanggal selesai wajib diisi.',
        'tglKeluar.required_if'   => 'Tanggal keluar wajib diisi.',
        'tglEfektif.required_if'  => 'Tanggal efektif wajib diisi.',
        'keterangan.required'     => 'Keterangan wajib diisi.',
      ]
    );

    $karyawan = Karyawan::findOrFail($this->karyawanId);

    if ($this->status === $karyawan->status) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Tidak ada perubahan status.'
      ]);
      return;
    }

    $this->storeData($karyawan, app(UpdateStatus::class));
  }

  public function storeData(Karyawan $karyawan, UpdateStatus $service)
  {
    try {
      $payload = match ($this->status) {
        'nonaktif' => [
          'tgl_keluar' => $this->tglKeluar,
          'keterangan' => $this->keterangan,
        ],

        'vakum' => [
          'tgl_mulai'   => $this->tglMulai,
          'tgl_selesai' => $this->tglSelesai,
          'keterangan'  => $this->keterangan,
        ],

        'aktif' => [
          'tgl_masuk'  => $this->tglEfektif ?? now()->toDateString(),
          'lokasi_id'  => $this->lokasiId ?? $karyawan->lokasi_id,
          'keterangan' => $this->keterangan,
        ],
      };

      $changed = $service->update(
        $karyawan,
        $this->status,
        $payload
      );

      if (! $changed) {
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Tidak ada perubahan data.'
        ]);
        return;
      }

      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Status berhasil diupdate.'
      ]);

      $this->dispatch('karyawan-updated');
      $this->dispatch('closeStatus');
    } catch (\Exception $e) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage(),
      ]);
    }
  }
}
