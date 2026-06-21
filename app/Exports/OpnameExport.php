<?php

namespace App\Exports;

use App\Models\StockOpname;
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

class OpnameExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
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
        $query = StockOpname::with(['barang', 'gudang', 'pengguna']);

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['month'])) {
            $date = Carbon::parse($this->filters['month']);
            $query->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
        }

        if (!empty($this->filters['gudang_id'])) {
            $query->where('gudang_id', $this->filters['gudang_id']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Barang',
            'Kode Barang',
            'Gudang',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Status',
            'Keterangan',
            'Auditor',
        ];
    }

    public function map($opname): array
    {
        $this->rowNumber++;

        $selisih = $opname->selisih;
        $status  = $selisih == 0 ? 'Match' : ($selisih > 0 ? '+' . $selisih . ' (Lebih)' : $selisih . ' (Kurang)');

        return [
            $this->rowNumber,
            $opname->created_at->format('d/m/Y H:i'),
            $opname->barang->nama_barang ?? '-',
            $opname->barang->kode_barang ?? '-',
            $opname->gudang->nama_gudang ?? '-',
            $opname->stok_sistem,
            $opname->stok_fisik,
            $selisih,
            $status,
            $opname->keterangan ?? '-',
            $opname->pengguna->nama_lengkap ?? '-',
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
                $sheet->setCellValue('B4', 'LAPORAN STOCK OPNAME (AUDIT STOK) — Dicetak: ' . date('d/m/Y H:i'));
                $sheet->setCellValue('B5', '');

                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B3')->getFont()->setSize(8)->getColor()->setRGB('555555');
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('92400E');
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->getColor()->setRGB('D97706');

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
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
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
                                  ->getStartColor()->setRGB('FFFBEB');
                        }
                        $statusVal = $sheet->getCell('I' . $r)->getValue();
                        if (str_contains((string)$statusVal, 'Match')) {
                            $sheet->getStyle('I' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                            $sheet->getStyle('I' . $r)->getFont()->getColor()->setRGB('15803D');
                        } elseif (str_contains((string)$statusVal, 'Lebih')) {
                            $sheet->getStyle('I' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
                            $sheet->getStyle('I' . $r)->getFont()->getColor()->setRGB('1E40AF');
                        } elseif (str_contains((string)$statusVal, 'Kurang')) {
                            $sheet->getStyle('I' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                            $sheet->getStyle('I' . $r)->getFont()->getColor()->setRGB('B91C1C');
                        }
                        $sheet->getStyle('I' . $r)->getFont()->setBold(true);
                        $sheet->getStyle('I' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(22);
                $sheet->getColumnDimension('K')->setWidth(18);

                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh SIDARLOG — Sistem Manajemen Logistik BPBD Kab. Tasikmalaya | ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setSize(8)->getColor()->setRGB('888888');
                $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
