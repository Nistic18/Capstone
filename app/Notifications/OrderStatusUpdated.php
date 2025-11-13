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
    $orderNumber = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
    $orderUrl = url('/orders/' . $this->order->id);

    return (new MailMessage)
        ->from('fishmarketnotification@gmail.com', 'FishMarket')
        ->subject('Your Order Status Updated')
        ->view('emails.order_status_updated', [
            'order' => $this->order,
            'notifiable' => $notifiable,
            'orderNumber' => $orderNumber,
            'orderUrl' => $orderUrl,
            'statusColor' => $this->getStatusColor($this->order->status),
        ]);
}
// Optional: helper function for status color
protected function getStatusColor($status)
{
    return match($status) {
        'Pending' => '#ffc107',
        'Processing' => '#17a2b8',
        'Completed' => '#28a745',
        'Cancelled' => '#dc3545',
        default => '#333',
    };
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
