<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class OpnameExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
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
            'Tanggal',
            'Barang',
            'Gudang',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Keterangan',
            'Auditor',
        ];
    }

    public function map($opname): array
    {
        $selisihFormatted = $opname->selisih == 0 ? 'Match' : ($opname->selisih > 0 ? '+' . $opname->selisih : $opname->selisih);

        return [
            $opname->created_at->format('d/m/Y H:i'),
            $opname->barang->nama_barang ?? '-',
            $opname->gudang->nama_gudang ?? '-',
            $opname->stok_sistem,
            $opname->stok_fisik,
            $selisihFormatted,
            $opname->keterangan ?? '-',
            $opname->pengguna->nama_lengkap ?? '-',
        ];
    }
}
