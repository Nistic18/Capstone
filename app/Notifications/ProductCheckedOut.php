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
    
    public $order;
    public $product;
    
    public function __construct($order, $product)
    {
        $this->order = $order;
        $this->product = $product;
    }
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        $orderNumber = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $orderUrl = url('/orders/' . $this->order->id);
        
        return (new MailMessage)
            ->from('fishmarketnotification@gmail.com', 'FishMarket')
            ->subject('Order Confirmation - Thank You for Your Purchase')
            ->view('emails.product_checked_out', [
                'order' => $this->order,
                'product' => $this->product,
                'notifiable' => $notifiable,
                'orderNumber' => $orderNumber,
                'orderUrl' => $orderUrl,
            ]);
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
            'status'     => 'checked out',
            'message'    => "Your product '{$this->product->name}' has been checked out!",
            'details'    => "Order #" . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . " includes your product '{$this->product->name}'.",
            'action_url' => url('/orders/' . $this->order->id), // Changed from route() to url()
        ];
    }
}