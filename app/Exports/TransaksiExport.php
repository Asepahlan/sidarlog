<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected string $jenis;
    protected array  $filters;

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
        return [
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
}
