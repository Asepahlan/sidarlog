<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $count = $this->items->count();
        return [
            'title' => 'Stok Barang Menipis',
            'message' => "Ada {$count} barang yang sudah mencapai batas stok minimal.",
            'icon' => 'fas fa-exclamation-triangle',
            'type' => 'warning',
            'url' => '/barang'
        ];
    }
}
