<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockSummaryNotification extends Notification
{
    use Queueable;

    protected int $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => 'Stok Barang Menipis',
            'message' => "Terdapat {$this->count} barang di bawah stok minimum.",
            'icon'    => 'fas fa-exclamation-triangle',
            'type'    => 'warning',
            'url'     => '/barang',
        ];
    }
}
