<?php

namespace App\Notifications;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpiredItemNotification extends Notification
{
    use Queueable;

    protected Item $item;
    protected string $type; // 'expired' | 'near_expired'

    public function __construct(Item $item, string $type = 'near_expired')
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
        $isExpired = $this->type === 'expired';
        $lokasi    = optional($this->item->lokasiBarang)->nama_lokasi ?? '-';
        $satuan    = optional($this->item->satuanKecil)->nama_satuan ?? 'pcs';
        $expDate   = $this->item->tgl_kadaluarsa;

        if ($isExpired) {
            $daysDiff = Carbon::today()->diffInDays($expDate);
            $statusText = "Sudah lewat {$daysDiff} hari";
        } else {
            $daysDiff = Carbon::today()->diffInDays($expDate);
            $statusText = "Expired dalam {$daysDiff} hari";
        }

        return [
            'title'       => $isExpired ? 'Barang Sudah Kadaluarsa!' : 'Barang Mendekati Kadaluarsa',
            'message'     => $isExpired
                ? "Barang \"{$this->item->nama_barang}\" sudah melewati tanggal kadaluarsa ({$expDate->format('d/m/Y')}). {$statusText}."
                : "Barang \"{$this->item->nama_barang}\" akan kadaluarsa pada {$expDate->format('d/m/Y')}. {$statusText}.",
            'icon'        => $isExpired ? 'fas fa-skull-crossbones' : 'fas fa-calendar-times',
            'type'        => $isExpired ? 'danger' : 'warning',
            'alert_kind'  => 'expiry',
            'url'         => '/barang',
            'item_id'     => $this->item->id,
            'kode_barang' => $this->item->kode_barang,
            'nama_barang' => $this->item->nama_barang,
            'lokasi'      => $lokasi,
            'tgl_kadaluarsa' => $expDate->format('Y-m-d'),
            'days_diff'      => $daysDiff,
            'status_text'    => $statusText,
            'stok_saat_ini'  => $this->item->stok_saat_ini_kecil ?? 0,
            'satuan'         => $satuan,
        ];
    }
}
