<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification
{
    use Queueable;

    protected Item $item;
    protected string $type; // 'low' | 'empty'

    public function __construct(Item $item, string $type = 'low')
    {
        $this->item = $item;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isEmpty = $this->type === 'empty';
        $lokasi  = optional($this->item->lokasiBarang)->nama_lokasi ?? '-';
        $satuan  = optional($this->item->satuanKecil)->nama_satuan ?? 'pcs';

        return [
            'title'       => $isEmpty ? 'Stok Barang Habis!' : 'Stok Barang Menipis',
            'message'     => $isEmpty
                ? "Stok \"{$this->item->nama_barang}\" telah habis (0 {$satuan})."
                : "Stok \"{$this->item->nama_barang}\" tinggal {$this->item->stok_saat_ini_kecil} {$satuan} (min: {$this->item->stok_minimal}).",
            'icon'        => $isEmpty ? 'fas fa-box-open' : 'fas fa-exclamation-triangle',
            'type'        => $isEmpty ? 'danger' : 'warning',
            'alert_kind'  => 'stock',
            'url'         => '/barang',
            'item_id'     => $this->item->id,
            'kode_barang' => $this->item->kode_barang,
            'nama_barang' => $this->item->nama_barang,
            'lokasi'      => $lokasi,
            'stok_saat_ini' => $this->item->stok_saat_ini_kecil ?? 0,
            'stok_minimal'  => $this->item->stok_minimal ?? 0,
            'satuan'        => $satuan,
        ];
    }
}
