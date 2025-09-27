<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductCheckedOut extends Notification
{
    use Queueable;

    protected $order;
    protected $product;

    public function __construct($order, $product)
    {
        $this->order = $order;
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database']; // You can also add 'mail' if needed
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
            'status'     => 'checked out',
            'message'    => "Your product '{$this->product->name}' has been checked out!",
            'details'    => "Order #{$this->order->id} includes your product '{$this->product->name}'.",
            'action_url' => route('orders.show', $this->order->id),
        ];
    }
}
