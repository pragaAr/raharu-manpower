<?php

namespace App\Livewire\Karyawan;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\File;

use Livewire\{
  Component,
  WithFileUploads
};

use Livewire\Attributes\Title;

use App\Models\{
  Kategori,
  Lokasi,
  Jabatan
};

use App\Services\Karyawan\{
  CreateData,
  PhotoUpload
};

use App\Services\Log\AuditLogger;

#[Title('Tambah Karyawan')]
class Create extends Component
{
  use AuthorizesRequests, WithFileUploads;

  public $kategori_id, $lokasi_id, $jabatan_id;
  public $nama, $ktp, $agama, $jk, $marital, $pendidikan;
  public $alamat, $telpon;
  public $bpjsTk, $bpjsKs;
  public $tglLahir, $tglMasuk, $tglEfektif, $tglMulai, $tglSelesai, $tglPenetapan;
  public $fotoUpload, $keterangan;

  // Custom error tracking untuk kontrak
  public $kontrakErrors = [];

  public $kategoris = [], $lokasis = [], $jabatans = [];

  public $backUrl;

  public function updatedFotoUpload()
  {
    $this->validateOnly(
      'fotoUpload',
      [
        'fotoUpload' => ['image', 'max:2048'],
      ],
      [
        'fotoUpload.image'  => 'File harus berupa gambar (jpg, jpeg, png, webp).',
        'fotoUpload.max'    => 'Ukuran foto maksimal 2MB.',
      ]
    );
  }

  public function mount()
  {
    $this->authorize('karyawan.create');

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

    $this->lokasis = Lokasi::orderBy('nama')->get([
      'id',
      'nama'
    ]);

    $this->backUrl = request('back') ? base64_decode(request('back')) : route('karyawan.index');
  }

  public function render()
  {
    return view('livewire.karyawan.create', [
      'pretitle'  => 'Form',
      'title'     => 'Tambah Karyawan'
    ]);
  }

  public function save()
  {
    $this->authorize('karyawan.create');

    $kategori     = Kategori::find($this->kategori_id);
    $isProbation  = $kategori && strtolower($kategori->nama) === 'probation';
    $isPkwt       = $kategori && strtolower($kategori->nama) === 'pkwt';
    $isPkwtt      = $kategori && strtolower($kategori->nama) === 'pkwtt';

    $this->validate(
      [
        'kategori_id'   => ['required'],
        'lokasi_id'     => ['required'],
        'jabatan_id'    => [$isProbation ? 'nullable' : 'required'],
        'nama'          => ['required', 'string', 'max:255'],
        'ktp'           => ['required', 'digits:16', 'unique:karyawan,ktp'],
        'agama'         => ['required', 'string', 'max:50'],
        'alamat'        => ['required', 'string', 'max:255'],
        'telpon'        => ['required', 'string', 'max:20'],
        'jk'            => ['required', 'in:l,p'],
        'marital'       => ['required', 'string', 'max:50'],
        'pendidikan'    => ['required', 'string', 'max:100'],
        'fotoUpload'    => ['nullable', 'image', 'max:2048'],
        'tglLahir'      => ['required', 'date'],
        'tglMasuk'      => ['required', 'date'],
        'tglEfektif'    => ['required', 'date'],
        'tglMulai'      => [$isPkwt ? ['required', 'date'] : 'nullable'],
        'tglSelesai'    => [$isPkwt ? ['required', 'date', 'after_or_equal:tglMulai'] : 'nullable'],
        'tglPenetapan'  => [$isPkwtt ? ['required', 'date'] : 'nullable'],
      ],
      [
        'kategori_id.required'      => 'Kategori wajib dipilih.',
        'lokasi_id.required'        => 'Penempatan wajib dipilih.',
        'jabatan_id.required'       => 'Jabatan wajib dipilih.',
        'nama.required'             => 'Nama wajib diisi.',
        'ktp.required'              => 'KTP wajib diisi.',
        'ktp.digits'                => 'KTP harus 16 digit angka.',
        'ktp.unique'                => 'KTP sudah digunakan.',
        'agama.required'            => 'Agama wajib diisi.',
        'alamat.required'           => 'Alamat wajib diisi.',
        'alamat.max'                => 'Alamat maksimal 255 karakter.',
        'telpon.required'           => 'Telpon wajib diisi.',
        'jk.required'               => 'Jenis kelamin wajib diisi.',
        'jk.in'                     => 'Jenis kelamin tidak valid.',
        'marital.required'          => 'Marital wajib diisi.',
        'pendidikan.required'       => 'Pendidikan wajib diisi.',
        'fotoUpload.image'          => 'Foto harus berupa gambar.',
        'fotoUpload.max'            => 'Ukuran foto maksimal 2MB.',
        'tglLahir.required'         => 'Tanggal lahir wajib diisi.',
        'tglLahir.date'             => 'Wajib menggunakan format tanggal valid',
        'tglMasuk.required'         => 'Tanggal masuk wajib diisi.',
        'tglMasuk.date'             => 'Wajib menggunakan format tanggal valid',
        'tglEfektif.required'       => 'Tanggal efektif wajib diisi.',
        'tglEfektif.date'           => 'Wajib menggunakan format tanggal valid',
        'tglMulai.required'         => 'Tanggal mulai wajib diisi.',
        'tglMulai.date'             => 'Wajib menggunakan format tanggal valid',
        'tglSelesai.required'       => 'Tanggal selesai wajib diisi.',
        'tglSelesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        'tglSelesai.date'           => 'Wajib menggunakan format tanggal valid',
      ]
    );

    $this->storeData(app(CreateData::class));
  }

  public function storeData(CreateData $service)
  {
    $photoUploadService = app(PhotoUpload::class);

    $imgPath = $photoUploadService->handleUpload($this->fotoUpload);

    try {
      $service->create([
        'kategori_id'   => $this->kategori_id,
        'lokasi_id'     => $this->lokasi_id,
        'jabatan_id'    => $this->jabatan_id,
        'nama'          => $this->nama,
        'ktp'           => $this->ktp,
        'agama'         => $this->agama,
        'alamat'        => $this->alamat,
        'telpon'        => $this->telpon,
        'jenis_kelamin' => $this->jk,
        'marital'       => $this->marital,
        'pendidikan'    => $this->pendidikan,
        'bpjs_tk'       => $this->bpjsTk,
        'bpjs_ks'       => $this->bpjsKs,
        'tgl_lahir'     => $this->tglLahir,
        'tgl_masuk'     => $this->tglMasuk,
        'tgl_efektif'   => $this->tglEfektif,
        'tgl_mulai'     => $this->tglMulai,
        'tgl_selesai'   => $this->tglSelesai,
        'tgl_penetapan' => $this->tglPenetapan,
        'img'           => $imgPath,
        'keterangan'    => $this->keterangan,
      ]);

      $this->resetForm();
      $this->dispatch('resetSelect');
      $this->dispatch('focusFirstInput');
      $this->dispatch('alert', [
        'type'    => 'success',
        'message' => 'Data berhasil ditambah.'
      ]);
    } catch (\Throwable $e) {
      AuditLogger::error('Create karyawan', [
        'nama'        => $this->nama,
        'lokasi_id'   => $this->lokasi_id,
        'jabatan_id'  => $this->jabatan_id,
        'error'       => $e->getMessage(),
      ]);

      if ($imgPath !== 'uploads/default.webp') {
        File::delete(public_path($imgPath));
      }

      $this->dispatch('alert', [
        'type'    => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  private function resetForm()
  {
    $this->reset([
      'kategori_id',
      'lokasi_id',
      'jabatan_id',
      'nama',
      'ktp',
      'agama',
      'alamat',
      'telpon',
      'jk',
      'marital',
      'pendidikan',
      'bpjsTk',
      'bpjsKs',
      'tglLahir',
      'tglMasuk',
      'tglEfektif',
      'tglMulai',
      'tglSelesai',
      'tglPenetapan',
      'fotoUpload',
      'keterangan',
    ]);
  }
}
