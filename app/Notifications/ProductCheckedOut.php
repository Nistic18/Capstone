<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class ProductCheckedOut extends Notification implements ShouldQueue
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
        return ['mail', 'database']; // You can also add 'mail' if needed
    }
    // Add this method if using the 'mail' channel
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Product Checked Out')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line("Your product '{$this->product->name}' has been checked out in order #{$this->order->id}.")
                    ->action('View Order', url('/orders/' . $this->order->id))
                    ->line('Thank you for using our application!');
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
