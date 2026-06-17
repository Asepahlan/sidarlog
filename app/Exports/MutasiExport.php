<?php

namespace App\Exports;

use App\Models\StockMutation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class MutasiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
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
            'No. Mutasi',
            'Barang',
            'Gudang Asal',
            'Gudang Tujuan',
            'Jumlah',
            'Status',
            'Tanggal Mutasi',
            'Keterangan',
            'Pembuat',
        ];
    }

    public function map($mutation): array
    {
        return [
            $mutation->no_mutasi,
            $mutation->barang->nama_barang ?? '-',
            $mutation->gudangAsal->nama_gudang ?? '-',
            $mutation->gudangTujuan->nama_gudang ?? '-',
            $mutation->jumlah_barang_kecil . ' unit',
            strtoupper($mutation->status),
            $mutation->tgl_mutasi ? $mutation->tgl_mutasi->format('d/m/Y') : '-',
            $mutation->keterangan ?? '-',
            $mutation->pembuat->nama_lengkap ?? '-',
        ];
    }
}
