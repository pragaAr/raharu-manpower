<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Jabatan;

class WorkRuleSeeder extends Seeder
{
  public function run(): void
  {
    // Mapping: jabatan nama => work_rule config
    // Berdasarkan rules di rules-jadwal-lembur-shift-absensi.txt
    $ruleDefinitions = [
      // ============================================
      // Satpam: shift, no auto overtime, allow double shift & swap
      // ============================================
      'satpam' => [
        'rule' => [
          'use_shift'              => true,
          'auto_overtime'          => false,
          'overtime_need_approval' => true,
          'cuti_need_approval'     => true,
          'allow_double_shift'     => true,
          'allow_shift_swap'       => true,
        ],
        'days' => [
          // Senin-Minggu semua aktif (libur ditentukan di jadwal_karyawan)
          1 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          2 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          3 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          4 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          5 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          6 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
          7 => ['jam_masuk' => null, 'jam_pulang' => null, 'is_workday' => true],
        ],
      ],

      // ============================================
      // Sopir/Kernet/Mandor Kurir: senin-sabtu, auto overtime
      // ============================================
      'sopir_kurir' => [
        'jabatan_names' => ['sopir loper', 'loper', 'mandor paket', 'mandor loper'],
        'rule' => [
          'use_shift'              => false,
          'auto_overtime'          => true,
          'overtime_need_approval' => false,
          'cuti_need_approval'     => true,
          'allow_double_shift'     => false,
          'allow_shift_swap'       => false,
        ],
        'days' => [
          1 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          2 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          3 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          4 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          5 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          6 => ['jam_masuk' => '08:00', 'jam_pulang' => '14:00', 'is_workday' => true],
          7 => ['jam_masuk' => null,    'jam_pulang' => null,    'is_workday' => false],
        ],
      ],

      // ============================================
      // Bongkar muat / Mandor gudang: senin-minggu, auto overtime, libur acak
      // ============================================
      'gudang' => [
        'jabatan_names' => ['bongkar muat'],
        'rule' => [
          'use_shift'              => false,
          'auto_overtime'          => true,
          'overtime_need_approval' => false,
          'cuti_need_approval'     => true,
          'allow_double_shift'     => false,
          'allow_shift_swap'       => false,
        ],
        'days' => [
          1 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          2 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          3 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          4 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          5 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          6 => ['jam_masuk' => '08:00', 'jam_pulang' => '14:00', 'is_workday' => true],
          7 => ['jam_masuk' => '09:00', 'jam_pulang' => '15:00', 'is_workday' => true],
        ],
      ],

      // ============================================
      // Bengkel (montir, mandor bengkel): senin-minggu, NO auto overtime
      // ============================================
      // Note: belum ada jabatan bengkel di seeder existing, skip jika tidak ada

      // ============================================
      // Admin/CS/Pajak/IT/Marketing/Manager/OB: senin-sabtu, no auto overtime
      // ============================================
      'office' => [
        'jabatan_names' => [
          'direktur', 'komisaris', 'manager', 'staff', 'programmer',
          'supervisor', 'customer service',
        ],
        'rule' => [
          'use_shift'              => false,
          'auto_overtime'          => false,
          'overtime_need_approval' => true,
          'cuti_need_approval'     => true,
          'allow_double_shift'     => false,
          'allow_shift_swap'       => false,
        ],
        'days' => [
          1 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          2 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          3 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          4 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          5 => ['jam_masuk' => '08:00', 'jam_pulang' => '16:00', 'is_workday' => true],
          6 => ['jam_masuk' => '08:00', 'jam_pulang' => '14:00', 'is_workday' => true],
          7 => ['jam_masuk' => null,    'jam_pulang' => null,    'is_workday' => false],
        ],
      ],
    ];

    foreach ($ruleDefinitions as $key => $definition) {
      $jabatanList = [];

      if ($key === 'satpam') {
        // Satpam belum ada di JabatanSeeder, cari atau buat placeholder query
        $jabatan = Jabatan::where('nama', 'like', '%satpam%')->first();
        if ($jabatan) {
          $jabatanList[] = $jabatan;
        }
      } else {
        $names = $definition['jabatan_names'] ?? [];
        $jabatanList = Jabatan::whereIn('nama', $names)->get();
      }

      foreach ($jabatanList as $jabatan) {
        // Cek apakah sudah ada work_rule untuk jabatan ini
        $existing = DB::table('work_rule')
          ->where('jabatan_id', $jabatan->id)
          ->first();

        if ($existing) {
          continue; // sudah ada, skip
        }

        // Insert work_rule
        $workRuleId = DB::table('work_rule')->insertGetId(array_merge(
          $definition['rule'],
          [
            'jabatan_id' => $jabatan->id,
            'created_at' => now(),
            'updated_at' => now(),
          ]
        ));

        // Insert work_rule_days
        foreach ($definition['days'] as $dayOfWeek => $dayConfig) {
          DB::table('work_rule_days')->insert([
            'work_rule_id' => $workRuleId,
            'day_of_week'  => $dayOfWeek,
            'jam_masuk'    => $dayConfig['jam_masuk'],
            'jam_pulang'   => $dayConfig['jam_pulang'],
            'is_workday'   => $dayConfig['is_workday'],
            'created_at'   => now(),
            'updated_at'   => now(),
          ]);
        }
      }
    }
  }
}
