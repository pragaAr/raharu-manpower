<?php

namespace App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Component;
use Livewire\Attributes\Title;

use App\Services\Dashboard\DashboardService;

#[Title('Home')]
class Home extends Component
{
  use AuthorizesRequests;

  public $totalKaryawan;
  public $aktif;
  public $nonaktif;
  public int $kontrakHabis          = 0;
  public array $trenKaryawan        = [];
  public array $kategoriDistribusi  = [];

  public function mount(DashboardService $dashboard)
  {
    $summary = $dashboard->getSummary();

    $this->totalKaryawan  = $summary['total'];
    $this->aktif          = $summary['aktif'];
    $this->nonaktif       = $summary['nonaktif'];
    $this->kontrakHabis   = $summary['kontrakHabis'];

    $this->trenKaryawan       = $dashboard->getTrenKaryawan();
    $this->kategoriDistribusi = $dashboard->getDistribusiKategori();
  }

  public function render()
  {
    return view('livewire.home', [
      'title'       => 'Home',
      'hasActions'  => auth()->user()->canAny([
        'home.view',
      ]),
    ]);
  }
}
