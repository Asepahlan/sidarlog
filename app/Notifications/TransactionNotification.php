<?php

namespace App\Notifications;

use App\Models\StockTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification
{
    use Queueable;

    protected StockTransaction $tx;

    public function __construct(StockTransaction $tx)
    {
        $this->tx = $tx;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $isMasuk = $this->tx->jenis === 'masuk';
        $namaBarang = $this->tx->item?->nama_barang ?? 'Barang';
        return [
            'title'   => $isMasuk ? 'Barang Masuk Dicatat' : 'Barang Keluar Dicatat',
            'message' => "{$namaBarang} — {$this->tx->jumlah_barang_kecil} unit ({$this->tx->no_referensi})",
            'icon'    => $isMasuk ? 'fas fa-arrow-right-to-bracket' : 'fas fa-arrow-right-from-bracket',
            'type'    => $isMasuk ? 'info' : 'warning',
            'url'     => $isMasuk ? '/barang-masuk' : '/barang-keluar',
            'tx_id'   => $this->tx->id,
        ];
    }
}
