<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Component;
use Livewire\Attributes\Title;

use App\Models\{
  Karyawan,
  Kategori,
  Lokasi,
  Jabatan
};

use App\Services\Karyawan\MutasiData;
use App\Services\Log\AuditLogger;

#[Title('Mutasi Karyawan')]
class Mutasi extends Component
{
  use AuthorizesRequests;

  public $karyawanId, $jabatanId, $kategoriId, $lokasiId, $keterangan;

  public $karyawans = [], $kategoris = [], $lokasis = [], $jabatans = [];

  public $backUrl;

  public $old = [
    'kategori'  => null,
    'lokasi'    => null,
    'jabatan'   => null,
    'divisi'    => null,
    'unit'      => null,
    'efektif'   => null,
    'tmk'       => null,
    'thk'       => null,
    'penetapan' => null,
  ];

  public $new = [
    'kategori'  => null,
    'lokasi'    => null,
    'jabatan'   => null,
    'divisi'    => null,
    'unit'      => null,
    'efektif'   => null,
    'tmk'       => null,
    'thk'       => null,
    'penetapan' => null,
  ];

  protected function loadKaryawanData(): void
  {
    $this->karyawans = Karyawan::where('status', 'aktif')
      ->where(function ($q) {
        $q
          ->whereHas(
            'kategori',
            fn($k) => $k->where('nama', '!=', 'pkwt')
          )
          ->orWhere(function ($q) {
            $q->whereHas(
              'kategori',
              fn($k) => $k->where('nama', 'pkwt')
            )->whereHas('kontrakAktifUntukMutasi');
          });
      })
      ->orderBy('nik')
      ->get(['id', 'nik', 'nama']);
  }

  public function mount()
  {
    $this->authorize('karyawan.mutasi');

    $this->loadKaryawanData();

    $this->kategoris = Kategori::orderBy('nama')->get([
      'id',
      'nama'
    ]);

    $this->jabatans = Jabatan::join('unit', 'jabatan.unit_id', '=', 'unit.id')
      ->orderBy('jabatan.nama')
      ->get([
        'jabatan.id',
        'jabatan.nama',
        'unit.nama as unit_nama'
      ]);

    $this->lokasis = Lokasi::orderBy('nama')->get(['id', 'nama']);

    $this->backUrl = request('back') ? base64_decode(request('back')) : route('karyawan.index');
  }

  public function render()
  {
    return view('livewire.karyawan.mutasi', [
      'pretitle'  => 'Form',
      'title'     => 'Mutasi Karyawan'
    ]);
  }

  public function updatedKaryawanId($karyawanId)
  {
    if (!$karyawanId) {
      $this->reset('old');
      return;
    }

    $karyawan = Karyawan::with([
      'kategori',
      'lokasi',
      'jabatan.unit.divisi',
      'kontrakTerakhir'
    ])->find($karyawanId);

    if (!$karyawan) return;

    $this->old = [
      'kategori'  => $karyawan->kategori?->nama,
      'lokasi'    => $karyawan->lokasi?->nama,
      'jabatan'   => $karyawan->jabatan?->nama,
      'unit'      => $karyawan->jabatan?->unit?->nama,
      'divisi'    => $karyawan->jabatan?->unit?->divisi?->nama,
      'efektif'   => $karyawan->tgl_efektif,
      'tmk'       => $karyawan->kontrakTerakhir?->tgl_mulai,
      'thk'       => $karyawan->kontrakTerakhir?->tgl_selesai,
      'penetapan' => $karyawan->tgl_penetapan,
    ];
  }

  public function updatedJabatanId($jabatanId)
  {
    if (!$jabatanId) {
      $this->new['jabatan'] = null;
      $this->new['unit']    = null;
      $this->new['divisi']  = null;
      return;
    }

    $jabatan = Jabatan::with('unit.divisi')->find($jabatanId);

    $this->new = array_merge($this->new ?? [], [
      'jabatan' => $jabatan->nama,
      'unit'    => $jabatan->unit?->nama,
      'divisi'  => $jabatan->unit?->divisi?->nama,
    ]);
  }

  public function updatedKategoriId($id)
  {
    if (!$id) {
      $this->new['kategori'] = null;
      return;
    }
    $kategori   = Kategori::find($id);
    $this->new  = array_merge($this->new ?? [], ['kategori' => strtolower($kategori?->nama ?? '')]);
  }

  public function updatedLokasiId($id)
  {
    if (!$id) {
      $this->new['lokasi'] = null;
      return;
    }
    $lokasi     = Lokasi::find($id);
    $this->new  = array_merge($this->new ?? [], ['lokasi' => $lokasi?->nama]);
  }

  public function save(MutasiData $service)
  {
    $this->authorize('karyawan.mutasi');

    $this->validate(
      ['karyawanId' => 'required'],
      ['karyawanId.required' => 'Karyawan wajib dipilih.']
    );

    $this->kategoriId = filled($this->kategoriId) ? $this->kategoriId : null;
    $this->lokasiId   = filled($this->lokasiId)   ? $this->lokasiId   : null;
    $this->jabatanId  = filled($this->jabatanId)  ? $this->jabatanId  : null;

    if (!$this->hasMutationData()) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Minimal salah satu data harus diubah (kategori, lokasi, atau jabatan).'
      ]);
      return;
    }

    $oldKategori = strtolower($this->old['kategori'] ?? '');
    $newKategori = strtolower($this->new['kategori'] ?? '');

    $allowedTransitions = [
      'harian'      => ['pkwt'],
      'magang'      => ['pkwt'],
      'outsourcing' => ['pkwt'],
      'probation'   => ['pkwt'],
      'pkwt'        => ['pkwtt'],
      'pkwtt'       => ['harian'],
    ];

    if ($newKategori && isset($allowedTransitions[$oldKategori])) {
      if (!in_array($newKategori, $allowedTransitions[$oldKategori])) {
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Perubahan kategori tidak diizinkan.'
        ]);
        return;
      }
    }

    if (strtolower($oldKategori ?? '') === 'pkwt' && blank($newKategori) && !blank($this->new['penetapan'])) {
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Penetapan hanya untuk PKWTT'
      ]);
      return;
    }

    $rulesByTransition = [
      'harian:pkwt'       => ['new.tmk', 'new.thk', 'new.efektif', 'keterangan'],
      'magang:pkwt'       => ['new.tmk', 'new.thk', 'new.efektif', 'keterangan'],
      'outsourcing:pkwt'  => ['new.tmk', 'new.thk', 'new.efektif', 'keterangan'],
      'probation:pkwt'    => ['new.tmk', 'new.thk', 'new.efektif', 'keterangan'],
      'pkwt:pkwtt'        => ['new.penetapan', 'new.efektif', 'keterangan'],
      'pkwtt:harian'      => ['new.efektif', 'keterangan'],
    ];

    if ($newKategori) {
      $transitionKey = "{$oldKategori}:{$newKategori}";

      if (isset($rulesByTransition[$transitionKey])) {
        $rules    = [];
        $messages = [];

        foreach ($rulesByTransition[$transitionKey] as $field) {
          $rules[$field] = 'required';

          $label = ucfirst(str_replace(['new.', '_'], ['', ' '], $field));
          $messages["{$field}.required"] = "{$label} wajib diisi.";
        }

        $this->validate($rules, $messages);
      }
    }

    if (!$newKategori && ($this->lokasiId || $this->jabatanId)) {
      $this->validate(
        [
          'new.efektif' => ['required', 'date'],
          'keterangan'  => ['required', 'string'],
        ],
        [
          'new.efektif.required'  => 'Tanggal efektif wajib diisi.',
          'keterangan.required'   => 'Keterangan wajib diisi.',
        ]
      );
    }

    $this->storeData($service);
  }

  public function storeData(MutasiData $service)
  {
    $this->new = array_merge([
      'efektif'   => null,
      'tmk'       => null,
      'thk'       => null,
      'penetapan' => null,
    ], $this->new ?? []);

    try {
      $changed = $service->mutasi(
        [
          'karyawanId'  => $this->karyawanId,
          'lokasiId'    => $this->lokasiId,
          'jabatanId'   => $this->jabatanId,
          'kategoriId'  => $this->kategoriId,
          'keterangan'  => $this->keterangan
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
        'message' => 'Karyawan berhasil dimutasi.'
      ]);

      $this->resetForm();
      $this->dispatch('resetSelect');
      $this->loadKaryawanData();
      $this->dispatch('refresh-tomselect', [
        'karyawans' => $this->karyawans,
      ]);
    } catch (\Throwable $e) {
      AuditLogger::error('Mutasi karyawan gagal', [
        'karyawan_id' => $this->karyawanId,
        'error'       => $e->getMessage(),
      ]);
      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => 'Karyawan gagal dimutasi: ' . $e->getMessage()
      ]);
    }
  }

  protected function hasMutationData(): bool
  {
    return collect([
      $this->kategoriId,
      $this->jabatanId,
      $this->lokasiId,
      $this->new['efektif'] ?? null,
      $this->new['tmk'] ?? null,
      $this->new['thk'] ?? null,
      $this->new['penetapan'] ?? null,
    ])->filter(fn($v) => !blank($v))->isNotEmpty();
  }

  private function resetForm()
  {
    $this->reset([
      'karyawanId',
      'kategoriId',
      'lokasiId',
      'jabatanId',
      'keterangan',
    ]);

    $this->old = array_fill_keys(array_keys($this->old), null);
    $this->new = array_fill_keys(array_keys($this->new), null);
  }
}
