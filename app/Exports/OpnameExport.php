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
    private $firstRecord = null;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function collection()
    {
        $query = StockOpname::with(['barang.satuanKecil', 'gudang', 'pengguna']);

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

        if (!empty($this->filters['periode'])) {
            $query->where('periode', $this->filters['periode']);
        }

        if (!empty($this->filters['tgl_opname'])) {
            $query->whereDate('tgl_opname', $this->filters['tgl_opname']);
        }

        if (!empty($this->filters['kategori_id'])) {
            $query->whereHas('barang', function ($q) {
                $q->where('kategori_id', $this->filters['kategori_id']);
            });
        }

        $records = $query->orderBy('created_at', 'desc')->get();
        
        if ($records->isNotEmpty()) {
            $this->firstRecord = $records->first();
        }

        return $records;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Satuan',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Kondisi',
            'Keterangan',
            'Auditor / Petugas',
            'Waktu Perekaman',
        ];
    }

    public function map($opname): array
    {
        $this->rowNumber++;

        $selisih = $opname->selisih;
        $statusPrefix = $selisih > 0 ? '+' : '';

        return [
            $this->rowNumber,
            $opname->barang->nama_barang ?? '-',
            $opname->barang->satuanKecil->nama_satuan ?? 'Pcs',
            $opname->stok_sistem,
            $opname->stok_fisik,
            $statusPrefix . $selisih,
            $opname->kondisi_barang ?? 'Baik',
            $opname->keterangan ?? '-',
            $opname->petugas_nama ?? $opname->pengguna->nama_lengkap ?? '-',
            $opname->created_at->format('d/m/Y H:i'),
        ];
    }

    public function registerEvents(): array
    {
        $filters = $this->filters;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($filters) {
                $sheet   = $event->sheet->getDelegate();
                $lastCol = 'J';
                $lastRow = $sheet->getHighestRow();

                // ─── KOP SURAT (baris 1-4) ───────────────────────────────
                $sheet->mergeCells('A1:A4');
                $sheet->mergeCells('B1:' . $lastCol . '1');
                $sheet->mergeCells('B2:' . $lastCol . '2');
                $sheet->mergeCells('B3:' . $lastCol . '3');
                $sheet->mergeCells('B4:' . $lastCol . '4');

                $sheet->setCellValue('B1', 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA');
                $sheet->setCellValue('B2', 'BADAN PENANGGULANGAN BENCANA DAERAH');
                $sheet->setCellValue('B3', 'Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111 | bpbd@tasikmalayakab.go.id');
                $sheet->setCellValue('B4', 'Email: bpbd@tasikmalayakab.go.id & bpbd.tasikmalayakab@gmail.com  |  TASIKMALAYA - 46113');

                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(11)->setName('Times New Roman');
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B3:B4')->getFont()->setSize(8.5)->setName('Times New Roman');
                $sheet->getStyle('B3:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Border double garis hitam tebal di baris 4 Kop
                $sheet->getStyle('A4:' . $lastCol . '4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

                $sheet->getRowDimension(1)->setRowHeight(18);
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getRowDimension(3)->setRowHeight(13);
                $sheet->getRowDimension(4)->setRowHeight(13);

                // Logo
                $logoPath = public_path('img/logo-daerah.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo BPBD');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(65);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(15)->setOffsetY(2);
                    $drawing->setWorksheet($sheet);
                }

                // ─── JUDUL DOKUMEN & SUBTITLE (baris 5-7) ───────────────
                $sheet->mergeCells('A5:' . $lastCol . '5');
                $sheet->mergeCells('A6:' . $lastCol . '6');

                $sheet->setCellValue('A5', 'LAPORAN HASIL STOCK OPNAME (AUDIT STOK)');
                
                $nomorDok = 'SO/' . (isset($filters['periode']) ? str_replace(' ', '-', $filters['periode']) : date('Y-m')) . '/' . date('dmy');
                if ($this->firstRecord && !empty($this->firstRecord->nomor_stock_opname)) {
                    $nomorDok = $this->firstRecord->nomor_stock_opname;
                }
                
                $sheet->setCellValue('A6', 'Nomor : ' . $nomorDok);

                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(12)->setName('Times New Roman')->setUnderline(true);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A6')->getFont()->setSize(10)->setName('Times New Roman');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getRowDimension(5)->setRowHeight(20);
                $sheet->getRowDimension(6)->setRowHeight(16);

                // Pastikan gridlines Excel selalu terlihat
                $sheet->setShowGridlines(true);

                // Style Header Tabel
                $sheet->getStyle('A8:' . $lastCol . '8')->applyFromArray([
                    'font'      => ['bold' => true, 'name' => 'Times New Roman', 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(24);

                // ─── DATA ROWS STYLING ─────────────────────────────────
                if ($lastRow > 8) {
                    $sheet->getStyle('A9:' . $lastCol . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['name' => 'Times New Roman', 'size' => 9],
                    ]);

                    for ($r = 9; $r <= $lastRow; $r++) {
                        $sheet->getStyle('A' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        
                        $sheet->getStyle('D' . $r . ':F' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle('D' . $r . ':F' . $r)->getNumberFormat()->setFormatCode('#,##0;-#,##0;"-"');
                        
                        $sheet->getStyle('G' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('J' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Berikan warna selisih jika ada perbedaan (sekarang kolom F karena Kode Barang dihapus)
                        $selisihCell = $sheet->getCell('F' . $r)->getValue();
                        $selisihVal = (int)$selisihCell;
                        if ($selisihVal > 0) {
                            $sheet->getStyle('F' . $r)->getFont()->getColor()->setRGB('1E40AF'); // Blue for excess
                            $sheet->getStyle('F' . $r)->getFont()->setBold(true);
                        } elseif ($selisihVal < 0) {
                            $sheet->getStyle('F' . $r)->getFont()->getColor()->setRGB('B91C1C'); // Red for deficit
                            $sheet->getStyle('F' . $r)->getFont()->setBold(true);
                        }
                    }
                }

                // ─── TANDA TANGAN (SIGNATURES) ──────────────────────────
                $petugasNama = $this->firstRecord->petugas_nama ?? '......................................';
                $petugasNip = $this->firstRecord->petugas_nip ?? '-';
                
                $kepalaGudangNama = $this->firstRecord->kepala_gudang_nama ?? '......................................';
                $kepalaGudangNip = $this->firstRecord->kepala_gudang_nip ?? '-';
                
                $mengetahuiNama = $this->firstRecord->mengetahui_nama ?? 'RONI, A.Ks., M.M';
                $mengetahuiNip = $this->firstRecord->mengetahui_nip ?? '19690901 199303 1 004';

                $tglCetakText = 'Tasikmalaya, ' . Carbon::now()->translatedFormat('d F Y');

                $sigStartRow = $lastRow + 2;
                
                $sheet->setCellValue('B' . $sigStartRow, 'Petugas Stock Opname,');
                $sheet->setCellValue('E' . $sigStartRow, 'Mengetahui,');
                $sheet->setCellValue('E' . ($sigStartRow + 1), 'Kepala Pelaksana BPBD');
                $sheet->setCellValue('H' . $sigStartRow, $tglCetakText);
                $sheet->setCellValue('H' . ($sigStartRow + 1), 'Kepala Gudang,');

                $sheet->getStyle('B' . $sigStartRow)->getFont()->setBold(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('B' . $sigStartRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('E' . $sigStartRow . ':E' . ($sigStartRow + 1))->getFont()->setBold(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('E' . $sigStartRow . ':E' . ($sigStartRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->getStyle('H' . $sigStartRow . ':H' . ($sigStartRow + 1))->getFont()->setBold(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('H' . $sigStartRow . ':H' . ($sigStartRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Spasi tanda tangan
                $sigNameRow = $sigStartRow + 5;
                
                $sheet->setCellValue('B' . $sigNameRow, $petugasNama);
                if ($petugasNip && $petugasNip !== '-') {
                    $sheet->setCellValue('B' . ($sigNameRow + 1), 'NIP. ' . $petugasNip);
                }

                $sheet->setCellValue('E' . $sigNameRow, $mengetahuiNama);
                if ($mengetahuiNip && $mengetahuiNip !== '-') {
                    $sheet->setCellValue('E' . ($sigNameRow + 1), 'NIP. ' . $mengetahuiNip);
                }

                $sheet->setCellValue('H' . $sigNameRow, $kepalaGudangNama);
                if ($kepalaGudangNip && $kepalaGudangNip !== '-') {
                    $sheet->setCellValue('H' . ($sigNameRow + 1), 'NIP. ' . $kepalaGudangNip);
                }

                $sheet->getStyle('B' . $sigNameRow)->getFont()->setBold(true)->setUnderline(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('B' . ($sigNameRow + 1))->getFont()->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('B' . $sigNameRow . ':B' . ($sigNameRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('E' . $sigNameRow)->getFont()->setBold(true)->setUnderline(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('E' . ($sigNameRow + 1))->getFont()->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('E' . $sigNameRow . ':E' . ($sigNameRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('H' . $sigNameRow)->getFont()->setBold(true)->setUnderline(true)->setName('Times New Roman')->setSize(9.5);
                $sheet->getStyle('H' . ($sigNameRow + 1))->getFont()->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('H' . $sigNameRow . ':H' . ($sigNameRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ─── COLUMN WIDTHS ──────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(6);   // No
                $sheet->getColumnDimension('B')->setWidth(26);  // Nama Barang
                $sheet->getColumnDimension('C')->setWidth(12);  // Satuan
                $sheet->getColumnDimension('D')->setWidth(15);  // Stok Sistem
                $sheet->getColumnDimension('E')->setWidth(15);  // Stok Fisik
                $sheet->getColumnDimension('F')->setWidth(15);  // Selisih
                $sheet->getColumnDimension('G')->setWidth(15);  // Kondisi
                $sheet->getColumnDimension('H')->setWidth(24);  // Keterangan
                $sheet->getColumnDimension('I')->setWidth(24);  // Auditor / Petugas
                $sheet->getColumnDimension('J')->setWidth(18);  // Waktu Perekaman
            },
        ];
    }
}
