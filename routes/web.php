<?php

use Illuminate\Support\Facades\{
  Route,
  Auth
};

use App\Services\Karyawan\PdfExporter;

use App\Http\Controllers\Notification;

use App\Livewire\{
  Home,
  Login
};

use App\Livewire\Absensi\{
  Index as AbsensiIndex
};

use App\Livewire\Master\{
  Lokasi,
  Unit,
  Divisi,
  Jabatan,
  Kategori
};

use App\Livewire\Access\{
  Role,
  Permission,
  User
};

use App\Livewire\Karyawan\{
  Index as KaryawanIndex,
  Create,
  Mutasi,
  Renewal,
  Detail
};

Route::get('/', function () {
  return Auth::check()
    ? redirect()->route('home.index')
    : redirect()->route('login');
});

Route::get('/login', Login::class)->name('login');
Route::get('/logout', function () {
  Auth::logout();
  session()->invalidate();
  session()->regenerateToken();

  return redirect('/login')->with('alert', [
    'type'    => 'success',
    'message' => 'Anda telah logout, sampai jumpa kembali.'
  ]);
})->name('logout');

Route::middleware(['auth', 'can:web.access'])->group(function () {
  Route::get('/notification', [Notification::class, 'index']);

  Route::get('/home', Home::class)
    ->name('home.index')
    ->middleware('can:home.view');

  // Master
  Route::group([], function () {
    Route::get('/lokasi', Lokasi::class)
      ->name('lokasi.index')
      ->middleware('can:lokasi.view');
    Route::get('/divisi', Divisi::class)
      ->name('divisi.index')
      ->middleware('can:divisi.view');
    Route::get('/unit', Unit::class)
      ->name('unit.index')
      ->middleware('can:unit.view');
    Route::get('/jabatan', Jabatan::class)
      ->name('jabatan.index')
      ->middleware('can:jabatan.view');
    Route::get('/kategori', Kategori::class)
      ->name('kategori.index')
      ->middleware('can:kategori.view');
  });

  // System Management
  Route::group([], function () {
    Route::get('/role', Role::class)
      ->name('role.index')
      ->middleware('can:role.view');
    Route::get('/user', User::class)
      ->name('user.index')
      ->middleware('can:user.view');
    Route::get('/permission', Permission::class)
      ->name('permission.index')
      ->middleware('can:permission.view');
  });

  // Karyawan
  Route::prefix('karyawan')->name('karyawan.')->group(function () {

    Route::get('/', KaryawanIndex::class)
      ->name('index')
      ->middleware('can:karyawan.view');

    Route::get('/create', Create::class)
      ->name('create')
      ->middleware('can:karyawan.create');

    Route::get('/mutasi', Mutasi::class)
      ->name('mutasi')
      ->middleware('can:karyawan.mutasi');

    Route::get('/renewal', Renewal::class)
      ->name('renewal')
      ->middleware('can:karyawan.renewal');

    Route::get('/export-pdf', function (PdfExporter $exporter) {
      $filters = session('karyawan_export_pdf', []);

      return $exporter
        ->make($filters)
        ->stream('karyawan-preview.pdf');
    })->name('pdf.preview');

    Route::get('/{nik}', Detail::class)
      ->name('detail')
      ->middleware('can:karyawan.view');
  });

  // Absensi
  Route::prefix('absensi')->name('absensi.')->group(function () {
    Route::get('/', AbsensiIndex::class)
      ->name('absensi')
      ->middleware('can:absensi.view');
  });
});
