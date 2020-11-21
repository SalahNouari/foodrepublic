<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class deliveryNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

      
    public $notification;
    /**
     * Create a new event instance.
     *
     * @return void
     */
 
    public function __construct($order, $message, $title)
    {
        $this->notification = [
        'receiver_user'=> $order->delivery->token,
		'message' => $message,
		'push_type' => 'individual',
		'payload' => ['url' => '/delivery', 'id' => $order->id, 'status' => $order->status],
        'title'  => $title
        ];
    }
}
