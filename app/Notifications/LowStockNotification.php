<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected Item $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $lokasi  = optional($this->item->lokasiBarang)->nama_lokasi ?? '-';
        $satuan  = optional($this->item->satuanKecil)->nama_satuan ?? 'pcs';

        return [
            'title'       => 'Stok Barang Menipis',
            'message'     => "Stok \"{$this->item->nama_barang}\" tinggal {$this->item->stok_saat_ini_kecil} {$satuan} (min: {$this->item->stok_minimal}).",
            'icon'        => 'fas fa-exclamation-triangle',
            'type'        => 'warning',
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
