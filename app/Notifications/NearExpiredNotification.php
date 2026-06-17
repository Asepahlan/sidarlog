<?php

namespace App\Notifications;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NearExpiredNotification extends Notification
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
        $lokasi    = optional($this->item->lokasiBarang)->nama_lokasi ?? '-';
        $satuan    = optional($this->item->satuanKecil)->nama_satuan ?? 'pcs';
        $expDate   = $this->item->tgl_kadaluarsa;
        $daysDiff  = Carbon::today()->diffInDays($expDate);
        $statusText = "Expired dalam {$daysDiff} hari";

        return [
            'title'       => 'Barang Mendekati Kadaluarsa',
            'message'     => "Barang \"{$this->item->nama_barang}\" akan kadaluarsa pada " . ($expDate ? $expDate->format('d/m/Y') : '-') . ". {$statusText}.",
            'icon'        => 'fas fa-calendar-times',
            'type'        => 'warning',
            'alert_kind'  => 'expiry',
            'url'         => '/barang',
            'item_id'     => $this->item->id,
            'kode_barang' => $this->item->kode_barang,
            'nama_barang' => $this->item->nama_barang,
            'lokasi'      => $lokasi,
            'tgl_kadaluarsa' => $expDate ? $expDate->format('Y-m-d') : null,
            'days_diff'      => $daysDiff,
            'status_text'    => $statusText,
            'stok_saat_ini'  => $this->item->stok_saat_ini_kecil ?? 0,
            'satuan'         => $satuan,
        ];
    }
}
