<?php

namespace App\Exports;

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\{
  FromCollection,
  WithHeadings,
  ShouldAutoSize,
  WithCustomStartCell,
  WithDrawings,
  WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\{
  NumberFormat,
  Alignment
};

class KaryawanExcelExport implements FromCollection, WithHeadings, ShouldAutoSize, WithCustomStartCell, WithDrawings, WithEvents
{
  protected array $appliedFilters;

  public function __construct(
    protected Collection $data,
    array $appliedFilters = []
  ) {
    $this->appliedFilters = $appliedFilters;
  }

  public function collection()
  {
    return $this->data->values()->map(fn($k, $i) => [
      '#'             => $i + 1,
      'NIK'           => strtoupper($k->nik),
      'NAMA'          => strtoupper($k->nama),
      'KTP'           => $k->ktp,
      'JABATAN'       => strtoupper($k->jabatan?->nama),
      'DIVISI'        => strtoupper($k->jabatan?->unit?->divisi?->nama),
      'LOKASI'        => strtoupper($k->lokasi?->nama),
      'KATEGORI'      => strtoupper($k->kategori?->nama),
      'AGAMA'         => strtoupper($k->agama),
      'L/P'           => strtoupper($k->jenis_kelamin),
      'TELPON'        => $k->telpon,
      'TANGGAL LAHIR' => date('d-m-Y', strtotime($k->tgl_lahir)),
      'USIA'          => $k->usia,
      'PENDIDIKAN'    => strtoupper($k->pendidikan),
      'MARITAL'       => strtoupper($k->marital),
      'TANGGAL MASUK' => date('d-m-Y', strtotime($k->tgl_masuk)),
      'STATUS'        => strtoupper($k->status),
    ]);
  }

  public function headings(): array
  {
    return array_keys($this->collection()->first() ?? []);
  }

  public function drawings()
  {
    $drawing = new Drawing();
    $drawing->setName('Raharu Logo');
    $drawing->setDescription('Logo');
    $drawing->setPath(public_path('img/raharu-light.png'));
    $drawing->setHeight(40);
    $drawing->setCoordinates('A1');
    return $drawing;
  }

  public function startCell(): string
  {
    return 'A8';
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet        = $event->sheet->getDelegate();
        $filtersText  = $this->formatFilters();
        $now          = date('d-m-Y H:i');

        $sheet->mergeCells('A4:Q4');
        $sheet->setCellValue('A4', 'LIST DATA KARYAWAN');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getAlignment()
          ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells('A5:D5');
        $sheet->setCellValue('A5', "Tanggal Export: $now");
        $sheet->getStyle('A5')->getFont()->setSize(10);
        $sheet->getStyle('A5')->applyFromArray([
          'font' => [
            'size' => 10,
            'color' => ['rgb' => '6B7280'],
          ],
          'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $sheet->mergeCells('A6:D6');
        $sheet->setCellValue('A6', 'Filter On | ' . ucwords($filtersText));
        $sheet->getStyle('A6')->applyFromArray([
          'font' => [
            'size' => 10,
            'color' => ['rgb' => '6B7280'],
          ],
          'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A8:{$lastColumn}8")->getFont()->setBold(true);
        $sheet->getStyle("A8:{$lastColumn}8")
          ->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);

        // Border table
        $sheet->getStyle("A8:{$lastColumn}{$lastRow}")
          ->getBorders()->getAllBorders()
          ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Styling & formating list
        $centerColumns = ['A', 'J', 'M', 'Q'];

        foreach ($centerColumns as $col) {
          $sheet->getStyle("{$col}9:{$col}{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $numberColumns = ['D'];

        foreach ($numberColumns as $col) {
          $sheet->getStyle("{$col}9:{$col}{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER);
        }
      }
    ];
  }

  protected function formatFilters(): string
  {
    if (empty($this->appliedFilters)) {
      return '-';
    }

    return collect($this->appliedFilters)
      ->map(function ($value, $key) {
        $label = str_replace('_', ' ', ucwords($key));
        return "$label: $value";
      })
      ->implode(' , ');
  }
}
