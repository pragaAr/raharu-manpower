<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Divisi;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\Karyawan;

class CleanupDuplicates extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cleanup:duplicates';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Clean up duplicate records in master tables (Kategori, Lokasi, Divisi, Unit) and reassign relationships';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->info('Starting cleanup process...');

    DB::transaction(function () {
      $this->cleanupTable(Kategori::class, 'Kategori', 'nama', [['model' => Karyawan::class, 'fk' => 'kategori_id']]);
      $this->cleanupTable(Lokasi::class, 'Lokasi', 'nama', [['model' => Karyawan::class, 'fk' => 'lokasi_id']]);

      // Divisi may be used by Unit, Jabatan (maybe), Karyawan (maybe)
      $divisiRelations = [
        ['model' => Unit::class, 'fk' => 'divisi_id'],
      ];
      if (Schema::hasColumn('jabatan', 'divisi_id')) {
        $divisiRelations[] = ['model' => Jabatan::class, 'fk' => 'divisi_id'];
      }
      if (Schema::hasColumn('karyawan', 'divisi_id')) {
        $divisiRelations[] = ['model' => Karyawan::class, 'fk' => 'divisi_id'];
      }
      $this->cleanupTable(Divisi::class, 'Divisi', 'nama', $divisiRelations);

      // Unit may be used by Jabatan, Karyawan (maybe)
      $unitRelations = [
        ['model' => Jabatan::class, 'fk' => 'unit_id'],
      ];
      if (Schema::hasColumn('karyawan', 'unit_id')) {
        $unitRelations[] = ['model' => Karyawan::class, 'fk' => 'unit_id'];
      }
      $this->cleanupTable(Unit::class, 'Unit', 'nama', $unitRelations);
    });

    $this->info('Cleanup process completed successfully.');
  }

  private function cleanupTable($modelClass, $label, $groupColumn, $relations)
  {
    $this->info("Processing {$label}...");

    // Find duplicates
    $duplicates = $modelClass::select($groupColumn, DB::raw('count(*) as count'))
      ->groupBy($groupColumn)
      ->having('count', '>', 1)
      ->get();

    if ($duplicates->isEmpty()) {
      $this->info("No duplicates found for {$label}.");
      return;
    }

    $totalDeleted = 0;

    foreach ($duplicates as $duplicate) {
      $value = $duplicate->{$groupColumn};
      $this->line("Fixing duplicates for {$label}: {$value}");

      // Get all records for this value, order by ID (keep the first one)
      $records = $modelClass::where($groupColumn, $value)->orderBy('id')->get();

      if ($records->count() <= 1) continue;

      $master = $records->first();
      $toDelete = $records->slice(1);

      foreach ($toDelete as $record) {
        // Reassign relationships
        foreach ($relations as $relation) {
          $relatedModel = $relation['model'];
          $fk = $relation['fk'];

          $countObj = $relatedModel::where($fk, $record->id)->update([$fk => $master->id]);
          if ($countObj > 0) {
            $this->line("  - Moved {$countObj} items in " . class_basename($relatedModel) . " from ID {$record->id} to {$master->id}");
          }
        }

        // Delete the duplicate
        $record->delete();
        $totalDeleted++;
      }
    }

    $this->info("Deleted {$totalDeleted} duplicate records for {$label}.");
    $this->newLine();
  }
}
