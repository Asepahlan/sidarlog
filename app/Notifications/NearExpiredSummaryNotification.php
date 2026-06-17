<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NearExpiredSummaryNotification extends Notification
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
            'title'   => 'Barang Mendekati Kadaluarsa',
            'message' => "Terdapat {$this->count} barang yang akan kadaluarsa dalam 30 hari.",
            'icon'    => 'fas fa-calendar-times',
            'type'    => 'warning',
            'url'     => '/barang',
        ];
    }
}
