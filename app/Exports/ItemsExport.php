<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\FirstParty;
use App\Models\Warehouse;
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

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $gudangId;
    protected $month;
    private int $rowNumber = 0;

    public function __construct($gudangId = null, $month = null)
    {
        $this->gudangId = $gudangId;
        $this->month = $month ?: date('Y-m');
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $parsedDate = Carbon::parse($this->month . '-01');
        $startDate = $parsedDate->copy()->startOfMonth();
        $endDate = $parsedDate->copy()->endOfMonth();

        $items = Item::with(['kategori', 'satuanKecil', 'sumberAnggaran'])->get();

        foreach ($items as $item) {
            // Calculate mutation
            $item->saldo_awal = $this->calculateStockBefore($item->id, $this->gudangId, $startDate);
            $item->masuk = $this->calculateStockBetween($item->id, $this->gudangId, $startDate, $endDate, 'masuk');
            $item->keluar = $this->calculateStockBetween($item->id, $this->gudangId, $startDate, $endDate, 'keluar');
            $item->penyesuaian = $this->calculateStockBetween($item->id, $this->gudangId, $startDate, $endDate, 'penyesuaian');
            $item->sisa_akhir = $item->saldo_awal + $item->masuk - $item->keluar + $item->penyesuaian;

            $harga = $item->harga_satuan_kecil ?? 0;
            $item->saldo_awal_val = $item->saldo_awal * $harga;
            
            $masukQty = $item->masuk + ($item->penyesuaian > 0 ? $item->penyesuaian : 0);
            $item->masuk_val = $masukQty * $harga;
            
            $keluarQty = $item->keluar + ($item->penyesuaian < 0 ? abs($item->penyesuaian) : 0);
            $item->keluar_val = $keluarQty * $harga;
            
            $item->sisa_akhir_val = $item->sisa_akhir * $harga;
        }

        return $items;
    }

    public function headings(): array
    {
        // Heading sub-kolom (ditempatkan di baris 6)
        return [
            'No',
            'Nama Barang',
            'Sumber Anggaran',
            '(Diterima) Tanggal',
            'Saldo Awal',
            'Masuk',
            'Keluar',
            'Sisa Akhir',
            'Satuan',
            'Harga Satuan (Rp)',
            'Saldo Awal (Rp)',
            'Nilai Pemasukan (Rp)',
            'Nilai Pengeluaran (Rp)',
            'Sisa Akhir (Rp)',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $item->nama_barang,
            $item->sumberAnggaran ? $item->sumberAnggaran->nama_sumber . ' ' . $item->sumberAnggaran->tahun_anggaran : '-',
            $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-',
            $item->saldo_awal,
            $item->masuk,
            $item->keluar,
            $item->sisa_akhir,
            $item->satuanKecil->nama_satuan ?? 'Pcs',
            $item->harga_satuan_kecil ?? 0,
            $item->saldo_awal_val,
            $item->masuk_val,
            $item->keluar_val,
            $item->sisa_akhir_val,
            $item->deskripsi ? strip_tags($item->deskripsi) : '-',
        ];
    }

    public function registerEvents(): array
    {
        $gudangId = $this->gudangId;
        $month = $this->month;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($gudangId, $month) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'O';
                $lastRow = $sheet->getHighestRow();

                $gudang = $gudangId ? Warehouse::find($gudangId) : null;
                $namaGudang = $gudang ? $gudang->nama_gudang : 'Semua Gudang';

                $parsedDate = Carbon::parse($month . '-01');
                $monthNameUpper = strtoupper($parsedDate->translatedFormat('F Y'));

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

                // Border double garis hitam tebal di baris 4
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

                // ─── JUDUL DOKUMEN & SUBTITLE (baris 5-8) ───────────────
                $sheet->insertNewRowBefore(5, 5); // Insert rows to place title
                
                $sheet->mergeCells('A5:' . $lastCol . '5');
                $sheet->mergeCells('A6:' . $lastCol . '6');
                $sheet->mergeCells('A7:' . $lastCol . '7');
                
                $sheet->setCellValue('A5', 'DAFTAR PERSEDIAAN LOGISTIK');
                $sheet->setCellValue('A6', 'BADAN PENANGGULANGAN BENCANA DAERAH KABUPATEN TASIKMALAYA');
                $sheet->setCellValue('A7', 'BULAN ' . $monthNameUpper);

                $sheet->getStyle('A5:A7')->getFont()->setBold(true)->setSize(11)->setName('Times New Roman');
                $sheet->getStyle('A5:A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A9', 'Gudang: ' . $namaGudang);
                $sheet->getStyle('A9')->getFont()->setBold(true)->setName('Times New Roman');

                // ─── DOUBLE HEADER TABEL (Row 10 & 11) ─────────────────
                // headers are now starting at row 10 (since we inserted rows)
                $sheet->getRowDimension(10)->setRowHeight(20);
                $sheet->getRowDimension(11)->setRowHeight(22);

                // Baris 10: Tulis Header tingkat atas
                $sheet->setCellValue('A10', 'No');
                $sheet->setCellValue('B10', 'Nama Barang');
                $sheet->setCellValue('C10', 'Sumber Anggaran');
                $sheet->setCellValue('D10', '(Diterima) Tanggal');
                $sheet->setCellValue('E10', 'Mutasi Barang');
                $sheet->setCellValue('I10', 'Satuan');
                $sheet->setCellValue('J10', 'Harga Satuan (Rp)');
                $sheet->setCellValue('K10', 'Jumlah Nilai Barang');
                $sheet->setCellValue('O10', 'Keterangan');

                // Baris 11: Tulis sub-headers
                $sheet->setCellValue('E11', 'Saldo Awal');
                $sheet->setCellValue('F11', 'Masuk');
                $sheet->setCellValue('G11', 'Keluar');
                $sheet->setCellValue('H11', 'Sisa Akhir');
                $sheet->setCellValue('K11', 'Saldo Awal (Rp)');
                $sheet->setCellValue('L11', 'Nilai Pemasukan (Rp)');
                $sheet->setCellValue('M11', 'Nilai Pengeluaran (Rp)');
                $sheet->setCellValue('N11', 'Sisa Akhir (Rp)');

                // Merge vertikal untuk No, Nama, Anggaran, Tanggal, Satuan, Harga, Keterangan
                $sheet->mergeCells('A10:A11');
                $sheet->mergeCells('B10:B11');
                $sheet->mergeCells('C10:C11');
                $sheet->mergeCells('D10:D11');
                $sheet->mergeCells('I10:I11');
                $sheet->mergeCells('J10:J11');
                $sheet->mergeCells('O10:O11');

                // Merge horizontal untuk Mutasi Barang (E10:H10) & Jumlah Nilai Barang (K10:N10)
                $sheet->mergeCells('E10:H10');
                $sheet->mergeCells('K10:N10');

                // Style Header Tabel
                $sheet->getStyle('A10:' . $lastCol . '11')->applyFromArray([
                    'font'      => ['bold' => true, 'name' => 'Times New Roman', 'size' => 8.5],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);

                // Pastikan gridlines Excel selalu terlihat
                $sheet->setShowGridlines(true);

                // ─── DATA ROWS STYLING ─────────────────────────────────
                $newLastRow = $sheet->getHighestRow();
                if ($newLastRow > 11) {
                    $sheet->getStyle('A12:' . $lastCol . $newLastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['name' => 'Times New Roman', 'size' => 8.5],
                    ]);

                    // Format numbers & currency
                    for ($r = 12; $r <= $newLastRow; $r++) {
                        $sheet->getStyle('A' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('D' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('E' . $r . ':H' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('I' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        
                        // Gunakan format akuntansi tanpa desimal: jika 0 akan tampil sebagai strip (-)
                        $numFormat = '#,##0;-#,##0;"-"';
                        $sheet->getStyle('E' . $r . ':H' . $r)->getNumberFormat()->setFormatCode($numFormat);
                        $sheet->getStyle('J' . $r)->getNumberFormat()->setFormatCode($numFormat);
                        $sheet->getStyle('K' . $r . ':N' . $r)->getNumberFormat()->setFormatCode($numFormat);
                    }
                }

                // ─── GRAND TOTALS ROW ──────────────────────────────────
                $totalRow = $newLastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'Jumlah Total');
                $sheet->mergeCells('A' . $totalRow . ':I' . $totalRow);

                $sheet->setCellValue('K' . $totalRow, '=SUM(K12:K' . $newLastRow . ')');
                $sheet->setCellValue('L' . $totalRow, '=SUM(L12:L' . $newLastRow . ')');
                $sheet->setCellValue('M' . $totalRow, '=SUM(M12:M' . $newLastRow . ')');
                $sheet->setCellValue('N' . $totalRow, '=SUM(N12:N' . $newLastRow . ')');

                // Style total row: top thin border, bottom double border
                $sheet->getStyle('A' . $totalRow . ':' . $lastCol . $totalRow)->applyFromArray([
                    'font'    => ['bold' => true, 'name' => 'Times New Roman', 'size' => 8.5],
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                        'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ]
                ]);
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $totalFormat = '#,##0;-#,##0;"-"';
                $sheet->getStyle('K' . $totalRow . ':N' . $totalRow)->getNumberFormat()->setFormatCode($totalFormat);

                // ─── TERBILANG ROW ─────────────────────────────────────
                $terbilangRow = $totalRow + 1;
                $sheet->mergeCells('A' . $terbilangRow . ':' . $lastCol . $terbilangRow);
                
                // Hitung Terbilang
                $totalVal = 0;
                for ($r = 12; $r <= $newLastRow; $r++) {
                    $totalVal += (float)$sheet->getCell('N' . $r)->getValue();
                }
                
                $terbilangStr = $this->terbilang($totalVal);
                $sheet->setCellValue('A' . $terbilangRow, 'Terbilang: ' . $terbilangStr);
                $sheet->getStyle('A' . $terbilangRow)->getFont()->setItalic(true)->setName('Times New Roman')->setSize(8.5);

                // ─── TANDA TANGAN (SIGNATURES) ──────────────────────────
                $kabid = FirstParty::where('jabatan', 'like', '%Kepala Bidang%')->first();
                $pengelola = FirstParty::where('jabatan', 'like', '%Pengelola%')->first() 
                    ?? FirstParty::where('nama_pihak', 'like', '%UMAN%')->first();

                $kabidNama = $kabid ? $kabid->nama_pihak : 'CAHYONO RAHMAN, S.T';
                $kabidNip = $kabid ? $kabid->nip : '19720130 200501 1 001';
                $pengelolaNama = $pengelola ? $pengelola->nama_pihak : 'UMAN SUHERMAN, S.IP';
                $pengelolaNip = $pengelola ? $pengelola->nip : '19730713 200701 1 005';

                $tglCetakText = 'Tasikmalaya, ' . Carbon::now()->translatedFormat('F Y');

                $sigStartRow = $terbilangRow + 2;
                $sheet->setCellValue('B' . $sigStartRow, 'Mengetahui,');
                $sheet->setCellValue('B' . ($sigStartRow + 1), 'Kepala Bidang Kedaruratan dan Logistik');
                $sheet->setCellValue('B' . ($sigStartRow + 2), 'BPBD Kabupaten Tasikmalaya');

                $sheet->setCellValue('K' . $sigStartRow, $tglCetakText);
                $sheet->setCellValue('K' . ($sigStartRow + 1), 'Pengelola Logistik,');

                $sheet->getStyle('B' . $sigStartRow . ':B' . ($sigStartRow + 2))->getFont()->setBold(true)->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('B' . $sigStartRow . ':B' . ($sigStartRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K' . $sigStartRow . ':K' . ($sigStartRow + 1))->getFont()->setBold(true)->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('K' . $sigStartRow . ':K' . ($sigStartRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Spasi tanda tangan
                $sigNameRow = $sigStartRow + 6;
                $sheet->setCellValue('B' . $sigNameRow, $kabidNama);
                $sheet->setCellValue('B' . ($sigNameRow + 1), 'NIP. ' . $kabidNip);

                $sheet->setCellValue('K' . $sigNameRow, $pengelolaNama);
                $sheet->setCellValue('K' . ($sigNameRow + 1), 'NIP. ' . $pengelolaNip);

                $sheet->getStyle('B' . $sigNameRow)->getFont()->setBold(true)->setUnderline(true)->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('B' . ($sigNameRow + 1))->getFont()->setName('Times New Roman')->setSize(8);
                $sheet->getStyle('B' . $sigNameRow . ':B' . ($sigNameRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('K' . $sigNameRow)->getFont()->setBold(true)->setUnderline(true)->setName('Times New Roman')->setSize(8.5);
                $sheet->getStyle('K' . ($sigNameRow + 1))->getFont()->setName('Times New Roman')->setSize(8);
                $sheet->getStyle('K' . $sigNameRow . ':K' . ($sigNameRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ─── COLUMN WIDTHS ──────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(6);   // No
                $sheet->getColumnDimension('B')->setWidth(26);  // Nama Barang
                $sheet->getColumnDimension('C')->setWidth(16);  // Anggaran
                $sheet->getColumnDimension('D')->setWidth(15);  // Tanggal
                $sheet->getColumnDimension('E')->setWidth(12);  // Saldo Awal Qty
                $sheet->getColumnDimension('F')->setWidth(10);  // Masuk Qty
                $sheet->getColumnDimension('G')->setWidth(10);  // Keluar Qty
                $sheet->getColumnDimension('H')->setWidth(12);  // Sisa Akhir Qty
                $sheet->getColumnDimension('I')->setWidth(10);  // Satuan
                $sheet->getColumnDimension('J')->setWidth(16);  // Harga Satuan
                $sheet->getColumnDimension('K')->setWidth(18);  // Saldo Awal Rp
                $sheet->getColumnDimension('L')->setWidth(18);  // Masuk Rp
                $sheet->getColumnDimension('M')->setWidth(18);  // Keluar Rp
                $sheet->getColumnDimension('N')->setWidth(18);  // Sisa Akhir Rp
                $sheet->getColumnDimension('O')->setWidth(20);  // Keterangan

            },
        ];
    }

    private function calculateStockBefore($itemId, $gudangId, $date)
    {
        $queryIn = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'masuk');

        $queryOut = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'keluar');

        $queryAdj = StockTransaction::where('barang_id', $itemId)
            ->where('tgl_transaksi', '<', $date)
            ->where('jenis', 'penyesuaian');

        if ($gudangId) {
            $queryIn->where('gudang_id', $gudangId);
            $queryOut->where('gudang_id', $gudangId);
            $queryAdj->where('gudang_id', $gudangId);
        }

        return (int)$queryIn->sum('jumlah_barang_kecil') 
            - (int)$queryOut->sum('jumlah_barang_kecil') 
            + (int)$queryAdj->sum('jumlah_barang_kecil');
    }

    private function calculateStockBetween($itemId, $gudangId, $startDate, $endDate, $jenis)
    {
        $query = StockTransaction::where('barang_id', $itemId)
            ->whereBetween('tgl_transaksi', [$startDate, $endDate])
            ->where('jenis', $jenis);

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        return (int) $query->sum('jumlah_barang_kecil');
    }

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut($nilai % 1000000000);
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut($nilai % 1000000000000);
        }     
        return $temp;
    }

    private function terbilang($nilai) {
        if ($nilai <= 0) {
            return "Nol Rupiah";
        }
        $hasil = trim($this->penyebut($nilai));
        return ucwords($hasil) . " Rupiah";
    }
}
