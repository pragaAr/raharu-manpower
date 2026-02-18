<?php

namespace App\Livewire\Karyawan\Modal;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\{
  Component,
  WithFileUploads
};

use Livewire\Attributes\On;

use App\Models\Karyawan;
use App\Services\Karyawan\UpdateData;
use App\Services\Log\AuditLogger;

class Edit extends Component
{
  use WithFileUploads, AuthorizesRequests;

  public $karyawanId;
  public $nama, $ktp, $agama, $alamat, $telpon;
  public $jk, $marital, $pendidikan;
  public $bpjsTk, $bpjsKs;
  public $tglLahir;

  public $img;
  public $fotoUpload;

  #[On('open-edit')]
  public function open($id)
  {
    $this->authorize('karyawan.edit');

    $this->resetValidation();
    $this->reset();

    $karyawan = Karyawan::findOrFail($id);

    $this->karyawanId = $id;
    $this->nama       = $karyawan->nama;
    $this->telpon     = $karyawan->telpon;
    $this->ktp        = $karyawan->ktp;
    $this->tglLahir   = $karyawan->tgl_lahir->format('Y-m-d');
    $this->jk         = $karyawan->jenis_kelamin;
    $this->agama      = $karyawan->agama;
    $this->pendidikan = $karyawan->pendidikan;
    $this->marital    = $karyawan->marital;
    $this->alamat     = $karyawan->alamat;
    $this->bpjsTk     = $karyawan->bpjs_tk;
    $this->bpjsKs     = $karyawan->bpjs_ks;
    $this->img        = $karyawan->img;
    $this->fotoUpload = null;

    $this->dispatch('openEdit');
  }

  public function update(UpdateData $service)
  {
    $this->authorize('karyawan.edit');

    $this->validate(
      [
        'nama'        => ['required'],
        'ktp'         => ['required', 'digits:16', \Illuminate\Validation\Rule::unique('karyawan', 'ktp')->ignore($this->karyawanId)],
        'agama'       => ['required'],
        'alamat'      => ['required', 'max:255'],
        'telpon'      => ['required'],
        'jk'          => ['required'],
        'marital'     => ['required'],
        'pendidikan'  => ['required'],
        'fotoUpload'  => ['nullable', 'image', 'max:2048'],
      ],
      [
        'nama.required'       => 'Nama wajib diisi!',
        'ktp.required'        => 'KTP wajib diisi!',
        'ktp.digits:16'       => 'KTP wajib 16 digits',
        'agama.required'      => 'Agama wajib diisi!',
        'alamat.required'     => 'Alamat wajib diisi!',
        'telpon.required'     => 'No Telpon wajib diisi!',
        'jk.required'         => 'Jenis kelamin wajib dipilih!',
        'marital.required'    => 'Status marital wajib diisi!',
        'pendidikan.required' => 'Pendidikan wajib diisi!',
      ]
    );

    if ($this->karyawanId) {
      try {
        $karyawan = Karyawan::findOrFail($this->karyawanId);

        $service->update(
          $karyawan,
          $this->fotoUpload,
          [
            'nama'        => $this->nama,
            'ktp'         => $this->ktp,
            'agama'       => $this->agama,
            'alamat'      => $this->alamat,
            'telpon'      => $this->telpon,
            'jk'          => $this->jk,
            'marital'     => $this->marital,
            'pendidikan'  => $this->pendidikan,
            'bpjsTk'      => $this->bpjsTk,
            'bpjsKs'      => $this->bpjsKs,
            'tglLahir'    => $this->tglLahir,
          ]
        );

        $this->dispatch('alert', [
          'type'    => 'success',
          'message' => 'Data berhasil diupdate.',
        ]);

        $this->dispatch('closeEdit');
        $this->dispatch('karyawan-updated')->to(\App\Livewire\Karyawan\Index::class);
      } catch (\Exception $e) {
        AuditLogger::error('Edit karyawan gagal', [
          'karyawan_id' => $this->karyawanId,
          'error'       => $e->getMessage(),
        ]);

        $this->dispatch('closeEdit');
        $this->dispatch('alert', [
          'type'    => 'error',
          'message' => 'Terjadi kesalahan saat edit data.'
        ]);
      }
    }
  }
}
