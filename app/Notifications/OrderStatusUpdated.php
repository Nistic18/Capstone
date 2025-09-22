<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        // You can add 'database' for in-app notifications
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Order Status Updated')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('The status of your order #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . ' has been updated.')
            ->line('New Status: ' . $this->order->status)
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with us!');
    }

public function toDatabase($notifiable)
{
    return [
        'order_id' => $this->order->id,
        'status'   => $this->order->status,
        'total'    => $this->order->total_price,
        'message'  => 'Your order #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) .
                      ' has been updated to ' . $this->order->status,
    ];
}

}
