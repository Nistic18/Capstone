<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdated extends Notification
{
    use Queueable;
    
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $orderNumber = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $orderUrl = url('/orders/' . $this->order->id);
        
        // Determine the email subject based on status
        $subject = $this->getEmailSubject();
        
        return (new MailMessage)
            ->from('fishmarketnotification@gmail.com', 'FishMarket')
            ->subject($subject)
            ->view('emails.order_status_updated', [
                'order' => $this->order,
                'notifiable' => $notifiable,
                'orderNumber' => $orderNumber,
                'orderUrl' => $orderUrl,
                'statusColor' => $this->getStatusColor($this->order->status),
            ]);
    }

    protected function getEmailSubject()
    {
        $orderNumber = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        
        // Check refund status first
        if ($this->order->refund_status && $this->order->refund_status !== 'None') {
            switch($this->order->refund_status) {
                case 'Pending':
                    return "Refund Request Submitted - Order #$orderNumber";
                case 'Approved':
                    return "Refund Approved - Order #$orderNumber";
                case 'Rejected':
                    return "Refund Rejected - Order #$orderNumber";
            }
        }
        
        // Check if cancelled
        if ($this->order->status === 'Cancelled') {
            return "Order Cancelled - Order #$orderNumber";
        }
        
        // Regular status
        return "Order Status Updated - Order #$orderNumber";
    }

    protected function getStatusColor($status)
    {
        return match($status) {
            'Pending' => '#ffc107',
            'Packed' => '#ffc107',
            'Shipped' => '#17a2b8',
            'Delivered' => '#28a745',
            'Cancelled' => '#6c757d',
            'Refunded' => '#dc3545',
            default => '#333',
        };
    }

    public function toDatabase($notifiable)
    {
        $orderNumber = str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        
        // Create appropriate message based on status
        $message = $this->getDatabaseMessage($orderNumber);
        
        return [
            'order_id' => $this->order->id,
            'status'   => $this->order->status,
            'refund_status' => $this->order->refund_status,
            'total'    => $this->order->total_price,
            'message'  => $message,
        ];
    }

    protected function getDatabaseMessage($orderNumber)
    {
        // Check refund status first
        if ($this->order->refund_status && $this->order->refund_status !== 'None') {
            switch($this->order->refund_status) {
                case 'Pending':
                    return "Your refund request for order #$orderNumber has been submitted";
                case 'Approved':
                    return "Your refund for order #$orderNumber has been approved";
                case 'Rejected':
                    return "Your refund request for order #$orderNumber has been rejected";
            }
        }
        
        // Check if cancelled
        if ($this->order->status === 'Cancelled') {
            return "Your order #$orderNumber has been cancelled";
        }
        
        // Regular status
        return "Your order #$orderNumber has been updated to " . $this->order->status;
    }
}