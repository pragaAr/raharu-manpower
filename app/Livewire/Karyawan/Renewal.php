<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Component;
use Livewire\Attributes\Title;

use App\Models\Karyawan;

use App\Services\Karyawan\Renewalkontrak;
use App\Services\Log\AuditLogger;

#[Title('Renewal Karyawan')]
class Renewal extends Component
{
  use AuthorizesRequests;

  public $karyawanId, $keterangan;

  public $karyawans = [];

  public $backUrl;

  public $old = [
    'lokasi'  => null,
    'jabatan' => null,
    'divisi'  => null,
    'unit'    => null,
    'efektif' => null,
    'tmk'     => null,
    'thk'     => null,
  ];

  public $new = [
    'efektif' => null,
    'tmk'     => null,
    'thk'     => null,
  ];

  protected function loadKaryawanData(): void
  {
    $this->karyawans = Karyawan::kontrakAkanHabis()
      ->with('kontrakAktif')
      ->orderBy('nik')
      ->get();
  }

  public function mount()
  {
    $this->authorize('karyawan.mutasi');

    $this->loadKaryawanData();

    $this->backUrl = request('back') ? base64_decode(request('back')) : route('karyawan.index');
  }

  public function render()
  {
    return view('livewire.karyawan.renewal', [
      'pretitle'  => 'Form',
      'title'     => 'Renewal Karyawan'
    ]);
  }

  public function updatedKaryawanId($karyawanId)
  {
    if (!$karyawanId) {
      $this->reset('old');
      return;
    }

    $data = Karyawan::with([
      'lokasi',
      'jabatan.unit.divisi',
      'kontrakAktif',
    ])->find($karyawanId);

    if (!$data) return;

    $this->old = [
      'lokasi'  => $data->lokasi?->nama,
      'jabatan' => $data->jabatan?->nama,
      'unit'    => $data->jabatan?->unit?->nama,
      'divisi'  => $data->jabatan?->unit?->divisi?->nama,
      'efektif' => optional($data->tgl_masuk)?->format('Y-m-d'),
      'tmk'     => optional($data->kontrakAktif?->tgl_mulai)?->format('Y-m-d'),
      'thk'     => optional($data->kontrakAktif?->tgl_selesai)?->format('Y-m-d'),
    ];
  }

  public function save()
  {
    $this->authorize('karyawan.mutasi');

    $this->validate(
      [
        'karyawanId'  => ['required'],
        'new.efektif' => [
          'required',
          'date',
          function ($attr, $value, $fail) {
            if ($this->old['thk'] && $value <= $this->old['thk']) {
              $fail('Efektif harus lebih dari THK terakhir.');
            }
          },
        ],
        'new.tmk' => [
          'required',
          'date',
          function ($attr, $value, $fail) {
            if ($this->old['thk'] && $value <= $this->old['thk']) {
              $fail('TMK harus lebih dari THK terakhir.');
            }
          },
        ],
        'new.thk' => [
          'required',
          'date',
          'after:new.tmk',
        ],
        'keterangan' => ['required', 'string'],
      ],
      [
        'karyawanId.required'  => 'Karyawan wajib dipilih.',
        'new.efektif.required' => 'Tanggal efektif wajib diisi.',
        'new.tmk.required'     => 'TMK wajib diisi.',
        'new.thk.required'     => 'THK wajib diisi.',
        'new.thk.after'        => 'THK harus setelah tanggal mulai.',
        'keterangan.required'  => 'Keterangan wajib diisi.',
      ]
    );

    $this->storeData(app(Renewalkontrak::class));
  }

  public function storeData(Renewalkontrak $service)
  {
    try {
      $changed = $service->renewal(
        [
          'karyawanId' => $this->karyawanId,
          'keterangan' => $this->keterangan
        ],
        $this->new
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
        'message' => 'Kontrak berhasil diperpanjang.'
      ]);

      $this->resetForm();
      $this->dispatch('resetSelect');
      $this->loadKaryawanData();
      $this->dispatch('refresh-tomselect', [
        'karyawans' => $this->karyawans,
      ]);
      $this->dispatch('refresh-notification');
    } catch (\Throwable $e) {
      AuditLogger::error('Renewal karyawan gagal', [
        'karyawan_id' => $this->karyawanId,
        'error'       => $e->getMessage(),
      ]);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Kontrak gagal diperpanjang: ' . $e->getMessage()
      ]);
    }
  }

  private function resetForm()
  {
    $this->reset([
      'karyawanId',
      'keterangan',
    ]);

    $this->old = array_fill_keys(array_keys($this->old), null);
    $this->new = array_fill_keys(array_keys($this->new), null);
  }
}
