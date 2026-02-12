<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Title;

use App\Models\{
  Karyawan,
  History
};

#[Title('Detail Karyawan')]
class Detail extends Component
{
  public $nik;
  public $karyawan;
  public $kontrakKe;
  public $activeTab = 'informasi';
  public $historyLimit = 3;
  public $totalHistory = 0;
  public $history;
  public $backUrl;

  public function mount($nik)
  {
    $this->nik      = $nik;
    $this->karyawan = Karyawan::with([
      'kategori',
      'lokasi',
      'jabatan.unit.divisi',
      'kontrakTerakhir'
    ])
      ->where('nik', $this->nik)
      ->firstOrFail();

    $this->kontrakKe = $this->karyawan->kontrakTerakhir?->kontrak_ke;

    $this->fetchHistory();

    $this->backUrl = request('back') ? base64_decode(request('back')) : route('karyawan.index');
  }

  public function loadMore()
  {
    $this->historyLimit += 3;
    $this->fetchHistory();
  }

  protected function fetchHistory()
  {
    $query = History::where('karyawan_id', $this->karyawan->id)
      ->orderBy('created_at', 'desc');

    $this->totalHistory = $query->count();
    $this->history      = $query->limit($this->historyLimit)->get();
  }

  public function render()
  {
    return view('livewire.karyawan.detail', [
      'pretitle'  => 'Data',
      'title'     => 'Detail Karyawan'
    ]);
  }
}
