<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Delivered Successfully!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . ' has been successfully delivered.')
            ->line('Thank you for confirming delivery by scanning your QR code!')
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('We appreciate your trust and hope to serve you again soon!');
    }
}
