<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithCustomStartCell, WithEvents
{
    protected string $jenis;
    protected array  $filters;
    private int $rowNumber = 0;

    public function __construct(string $jenis = 'semua', array $filters = [])
    {
        $this->jenis   = $jenis;
        $this->filters = $filters;
    }

    public function title(): string
    {
        return match ($this->jenis) {
            'masuk'  => 'Barang Masuk',
            'keluar' => 'Barang Keluar',
            default  => 'Semua Transaksi',
        };
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $query = StockTransaction::with(['barang', 'gudang', 'pengguna', 'pihakKesatu', 'pihakKedua', 'referenceBap']);

        if ($this->jenis !== 'semua') {
            $query->where('jenis', $this->jenis);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tgl_transaksi', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tgl_transaksi', '<=', $this->filters['end_date']);
        }

        return $query->orderBy('tgl_transaksi', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Referensi',
            'Tanggal',
            'Jenis',
            'Barang',
            'Kode Barang',
            'Jumlah (Kecil)',
            'Jumlah (Besar)',
            'Gudang',
            'Pihak Kesatu',
            'Pihak Kedua',
            'No. BAP',
            'Penerima / Penyerah',
            'Keterangan',
            'Petugas',
        ];
    }

    public function map($t): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $t->no_referensi ?? '-',
            $t->tgl_transaksi ? Carbon::parse($t->tgl_transaksi)->format('d/m/Y') : '-',
            strtoupper($t->jenis),
            $t->barang->nama_barang ?? '-',
            $t->barang->kode_barang ?? '-',
            $t->jumlah_barang_kecil ?? 0,
            $t->jumlah_barang_besar ?? 0,
            $t->gudang->nama_gudang ?? '-',
            $t->pihakKesatu?->nama_pihak ?? '-',
            $t->pihakKedua?->nama_pihak ?? '-',
            $t->referenceBap?->nomor_ba ?? '-',
            $t->penerima_penyerah ?? '-',
            $t->keterangan ?? '-',
            $t->pengguna->nama_lengkap ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        $jenis = $this->jenis;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($jenis) {
                $sheet      = $event->sheet->getDelegate();
                $lastCol    = 'O';
                $lastRow    = $sheet->getHighestRow();

                // ─── KOP ────────────────────────────────────────────────
                $sheet->mergeCells('A1:A5');
                $sheet->mergeCells('B1:' . $lastCol . '1');
                $sheet->mergeCells('B2:' . $lastCol . '2');
                $sheet->mergeCells('B3:' . $lastCol . '3');
                $sheet->mergeCells('B4:' . $lastCol . '4');
                $sheet->mergeCells('B5:' . $lastCol . '5');

                $judul = match($jenis) { 'masuk' => 'BARANG MASUK', 'keluar' => 'BARANG KELUAR', default => 'SEMUA TRANSAKSI' };

                $sheet->setCellValue('B1', 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA');
                $sheet->setCellValue('B2', 'BADAN PENANGGULANGAN BENCANA DAERAH');
                $sheet->setCellValue('B3', 'Jl. Otto Iskandardinata No. 19 Tasikmalaya  |  Telp/Fax (0265) 334111  |  Email: bpbd@tasikmalayakab.go.id  |  TASIKMALAYA 46113');
                $sheet->setCellValue('B4', 'LAPORAN ' . $judul . ' — Dicetak: ' . date('d/m/Y H:i'));
                $sheet->setCellValue('B5', '');

                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B3')->getFont()->setSize(8)->getColor()->setRGB('555555');
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('1E3A5F');
                $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:' . $lastCol . '5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);

                $sheet->getRowDimension(1)->setRowHeight(18);
                $sheet->getRowDimension(2)->setRowHeight(26);
                $sheet->getRowDimension(3)->setRowHeight(14);
                $sheet->getRowDimension(4)->setRowHeight(16);
                $sheet->getRowDimension(5)->setRowHeight(6);
                $sheet->getRowDimension(6)->setRowHeight(22);

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

                // Header tabel
                $headerColor = match($jenis) { 'masuk' => '15803D', 'keluar' => 'B91C1C', default => '1E3A5F' };
                $sheet->getStyle('A6:' . $lastCol . '6')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
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
                                  ->getStartColor()->setRGB('EFF6FF');
                        }
                        // Badge jenis
                        $jenisVal = $sheet->getCell('D' . $r)->getValue();
                        if ($jenisVal === 'MASUK') {
                            $sheet->getStyle('D' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                            $sheet->getStyle('D' . $r)->getFont()->getColor()->setRGB('15803D');
                        } elseif ($jenisVal === 'KELUAR') {
                            $sheet->getStyle('D' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                            $sheet->getStyle('D' . $r)->getFont()->getColor()->setRGB('B91C1C');
                        }
                        $sheet->getStyle('D' . $r)->getFont()->setBold(true);
                        $sheet->getStyle('D' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(28);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(18);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(16);
                $sheet->getColumnDimension('M')->setWidth(20);
                $sheet->getColumnDimension('N')->setWidth(22);
                $sheet->getColumnDimension('O')->setWidth(18);

                // Footer
                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastCol . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh SIDARLOG — Sistem Manajemen Logistik BPBD Kab. Tasikmalaya | ' . date('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->getFont()->setSize(8)->getColor()->setRGB('888888');
                $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
