<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    private int $rowNumber = 0;

    // Data mulai dari baris 6 (baris 1-5 untuk KOP)
    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        return Item::with(['kategori', 'satuanKecil', 'satuanBesar'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Stok Kecil',
            'Satuan Kecil',
            'Stok Besar',
            'Satuan Besar',
            'Stok Minimal',
            'Status',
            'Tgl Kadaluarsa',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;

        $stok    = $item->stok_saat_ini_kecil;
        $stokMin = $item->stok_minimal ?? 0;
        $status  = $stok <= 0 ? 'HABIS' : ($stokMin > 0 && $stok <= $stokMin ? 'RENDAH' : 'AMAN');

        return [
            $this->rowNumber,
            $item->kode_barang ?? '-',
            $item->nama_barang,
            $item->kategori->nama_kategori ?? '-',
            $item->stok_saat_ini_kecil,
            $item->satuanKecil->nama_satuan ?? '-',
            $item->stok_saat_ini_besar,
            $item->satuanBesar->nama_satuan ?? '-',
            $item->stok_minimal ?? 0,
            $status,
            $item->tgl_kadaluarsa ? $item->tgl_kadaluarsa->format('d/m/Y') : '-',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Header tabel (row 6)
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastCol    = 'K';
                $lastColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
                $lastRow    = $sheet->getHighestRow();

                // ─── KOP SURAT (baris 1-5) ───────────────────────────────
                // Merge untuk logo area
                $sheet->mergeCells('A1:A5');

                // Merge teks instansi
                $sheet->mergeCells('B1:' . $lastCol . '1');
                $sheet->mergeCells('B2:' . $lastCol . '2');
                $sheet->mergeCells('B3:' . $lastCol . '3');
                $sheet->mergeCells('B4:' . $lastCol . '4');
                $sheet->mergeCells('B5:' . $lastCol . '5');

                // Isi teks KOP
                $sheet->setCellValue('B1', 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA');
                $sheet->setCellValue('B2', 'BADAN PENANGGULANGAN BENCANA DAERAH');
                $sheet->setCellValue('B3', 'Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111');
                $sheet->setCellValue('B4', 'Email: bpbd@tasikmalayakab.go.id  |  TASIKMALAYA - 46113');
                $sheet->setCellValue('B5', '');

                // Style KOP
                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B3:B4')->getFont()->setSize(9)->getColor()->setRGB('555555');
                $sheet->getStyle('B3:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Border bawah KOP
                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(14);
                $sheet->getRowDimension(4)->setRowHeight(14);
                $sheet->getRowDimension(5)->setRowHeight(6);
                $sheet->getRowDimension(6)->setRowHeight(20);

                // Logo
                $logoPath = public_path('img/logo-daerah.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo BPBD');
                    $drawing->setDescription('Logo BPBD');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(85);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(3);
                    $drawing->setWorksheet($sheet);
                }

                // ─── STYLE HEADER TABEL (row 6) ────────────────────────
                $sheet->getStyle('A6:' . $lastCol . '6')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
                ]);

                // ─── STYLE DATA ─────────────────────────────────────────
                if ($lastRow > 6) {
                    $sheet->getStyle('A7:' . $lastCol . $lastRow)->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Alternating row colors
                    for ($r = 7; $r <= $lastRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle('A' . $r . ':' . $lastCol . $r)
                                  ->getFill()->setFillType(Fill::FILL_SOLID)
                                  ->getStartColor()->setRGB('F0FDF4');
                        }
                    }

                    // Warna status
                    for ($r = 7; $r <= $lastRow; $r++) {
                        $statusVal = $sheet->getCell('J' . $r)->getValue();
                        if ($statusVal === 'AMAN') {
                            $sheet->getStyle('J' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                            $sheet->getStyle('J' . $r)->getFont()->getColor()->setRGB('15803D');
                        } elseif ($statusVal === 'RENDAH') {
                            $sheet->getStyle('J' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF9C3');
                            $sheet->getStyle('J' . $r)->getFont()->getColor()->setRGB('854D0E');
                        } elseif ($statusVal === 'HABIS') {
                            $sheet->getStyle('J' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                            $sheet->getStyle('J' . $r)->getFont()->getColor()->setRGB('B91C1C');
                        }
                        $sheet->getStyle('J' . $r)->getFont()->setBold(true);
                        $sheet->getStyle('J' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // ─── COLUMN WIDTHS ──────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(8);   // Logo / No
                $sheet->getColumnDimension('B')->setWidth(16);  // Kode
                $sheet->getColumnDimension('C')->setWidth(30);  // Nama
                $sheet->getColumnDimension('D')->setWidth(16);  // Kategori
                $sheet->getColumnDimension('E')->setWidth(12);  // Stok Kecil
                $sheet->getColumnDimension('F')->setWidth(14);  // Satuan Kecil
                $sheet->getColumnDimension('G')->setWidth(12);  // Stok Besar
                $sheet->getColumnDimension('H')->setWidth(14);  // Satuan Besar
                $sheet->getColumnDimension('I')->setWidth(12);  // Stok Min
                $sheet->getColumnDimension('J')->setWidth(12);  // Status
                $sheet->getColumnDimension('K')->setWidth(14);  // Kadaluarsa

                // ─── FOOTER ─────────────────────────────────────────────
                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh SIDARLOG — Sistem Manajemen Logistik BPBD Kab. Tasikmalaya | ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setSize(8)->getColor()->setRGB('888888');
                $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
