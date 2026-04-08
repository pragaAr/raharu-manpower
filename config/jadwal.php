<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Shift Rotation
  |--------------------------------------------------------------------------
  | Urutan rotasi shift satpam. Default: pagi, sore, malam.
  */
  'shift_rotation' => ['pagi', 'sore', 'malam'],

  /*
  |--------------------------------------------------------------------------
  | Hari Per Shift dalam Rotasi
  |--------------------------------------------------------------------------
  | Berapa hari berturut-turut satu shift dijalankan sebelum pindah
  | ke shift berikutnya. Contoh:
  |   1 => P S M P S M ...
  |   2 => P P S S M M P P S S M M ...
  |   3 => P P P S S S M M M ...
  |
  | Untuk shift malam, nilai ini akan dibatasi oleh max_consecutive_malam.
  */
  'shift_days_per_rotation' => 2,

  /*
  |--------------------------------------------------------------------------
  | Maksimal Shift Malam Berturut-Turut
  |--------------------------------------------------------------------------
  | Batas maksimal hari berturut-turut untuk shift malam.
  | Sesuai rules: max_consecutive_shift malam = 2.
  */
  'max_consecutive_malam' => 2,

  /*
  |--------------------------------------------------------------------------
  | Maksimal Libur Berturut-Turut
  |--------------------------------------------------------------------------
  | Batas maksimal hari libur berturut-turut untuk satpam.
  */
  'max_libur_beruntun' => 2,

  /*
  |--------------------------------------------------------------------------
  | Maksimal Kerja Berturut-Turut
  |--------------------------------------------------------------------------
  | Batas maksimal hari masuk berturut-turut tanpa libur.
  | Jika set ke 6, maka hari ke-7 dipastikan libur.
  */
  'max_consecutive_work' => 6,

];
