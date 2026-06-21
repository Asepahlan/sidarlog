<?php

namespace App\Exports;

use App\Models\StockMutation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class MutasiExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $filters;
    private int $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $query = StockMutation::with(['barang', 'gudangAsal', 'gudangTujuan', 'pembuat']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_mutasi', 'like', '%' . $search . '%')
                  ->orWhereHas('barang', function ($itemQuery) use ($search) {
                      $itemQuery->where('nama_barang', 'like', '%' . $search . '%')
                                ->orWhere('kode_barang', 'like', '%' . $search . '%');
                  });
            });
        }

        if (!empty($this->filters['gudang_asal_id'])) {
            $query->where('gudang_asal_id', $this->filters['gudang_asal_id']);
        }

        if (!empty($this->filters['gudang_tujuan_id'])) {
            $query->where('gudang_tujuan_id', $this->filters['gudang_tujuan_id']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tgl_mutasi', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tgl_mutasi', '<=', $this->filters['end_date']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Mutasi',
            'Barang',
            'Kode Barang',
            'Gudang Asal',
            'Gudang Tujuan',
            'Jumlah (unit)',
            'Status',
            'Tanggal Mutasi',
            'Keterangan',
            'Pembuat',
        ];
    }

    public function map($mutation): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $mutation->no_mutasi,
            $mutation->barang->nama_barang ?? '-',
            $mutation->barang->kode_barang ?? '-',
            $mutation->gudangAsal->nama_gudang ?? '-',
            $mutation->gudangTujuan->nama_gudang ?? '-',
            $mutation->jumlah_barang_kecil,
            strtoupper($mutation->status),
            $mutation->tgl_mutasi ? Carbon::parse($mutation->tgl_mutasi)->format('d/m/Y') : '-',
            $mutation->keterangan ?? '-',
            $mutation->pembuat->nama_lengkap ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastCol = 'K';
                $lastRow = $sheet->getHighestRow();

                // ─── KOP ─────────────────────────────────────────────────
                $sheet->mergeCells('A1:A5');
                $sheet->mergeCells('B1:' . $lastCol . '1');
                $sheet->mergeCells('B2:' . $lastCol . '2');
                $sheet->mergeCells('B3:' . $lastCol . '3');
                $sheet->mergeCells('B4:' . $lastCol . '4');
                $sheet->mergeCells('B5:' . $lastCol . '5');

                $sheet->setCellValue('B1', 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA');
                $sheet->setCellValue('B2', 'BADAN PENANGGULANGAN BENCANA DAERAH');
                $sheet->setCellValue('B3', 'Jl. Otto Iskandardinata No. 19 Tasikmalaya  |  Telp/Fax (0265) 334111  |  Email: bpbd@tasikmalayakab.go.id');
                $sheet->setCellValue('B4', 'LAPORAN MUTASI ANTAR GUDANG — Dicetak: ' . date('d/m/Y H:i'));
                $sheet->setCellValue('B5', '');

                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B3')->getFont()->setSize(8)->getColor()->setRGB('555555');
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('3730A3');
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->getColor()->setRGB('4F46E5');

                $sheet->getRowDimension(1)->setRowHeight(18);
                $sheet->getRowDimension(2)->setRowHeight(26);
                $sheet->getRowDimension(3)->setRowHeight(14);
                $sheet->getRowDimension(4)->setRowHeight(16);
                $sheet->getRowDimension(5)->setRowHeight(6);
                $sheet->getRowDimension(6)->setRowHeight(22);

                $logoPath = public_path('img/logo-daerah.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo BPBD');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(85);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(5)->setOffsetY(3);
                    $drawing->setWorksheet($sheet);
                }

                // Header tabel
                $sheet->getStyle('A6:' . $lastCol . '6')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                // Data rows
                if ($lastRow > 6) {
                    $sheet->getStyle('A7:' . $lastCol . $lastRow)->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    for ($r = 7; $r <= $lastRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle('A' . $r . ':' . $lastCol . $r)
                                  ->getFill()->setFillType(Fill::FILL_SOLID)
                                  ->getStartColor()->setRGB('EEF2FF');
                        }
                        $statusVal = $sheet->getCell('H' . $r)->getValue();
                        if ($statusVal === 'APPROVED') {
                            $sheet->getStyle('H' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                            $sheet->getStyle('H' . $r)->getFont()->getColor()->setRGB('15803D');
                        } elseif ($statusVal === 'PENDING') {
                            $sheet->getStyle('H' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF9C3');
                            $sheet->getStyle('H' . $r)->getFont()->getColor()->setRGB('854D0E');
                        } else {
                            $sheet->getStyle('H' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                            $sheet->getStyle('H' . $r)->getFont()->getColor()->setRGB('B91C1C');
                        }
                        $sheet->getStyle('H' . $r)->getFont()->setBold(true);
                        $sheet->getStyle('H' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(24);
                $sheet->getColumnDimension('K')->setWidth(20);

                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh SIDARLOG — Sistem Manajemen Logistik BPBD Kab. Tasikmalaya | ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setSize(8)->getColor()->setRGB('888888');
                $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
