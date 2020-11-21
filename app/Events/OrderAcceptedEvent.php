<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderAcceptedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    /**
     * Create a new event instance.
     *
     * @return void
     */
   public function __construct($order)
    {
        $this->notification = [
        'receiver_user'=> $order->user->token,
        'message' => "Your order has been accepted",
        'push_type' => 'individual',
        'payload' => ['url' => '/cart', 'id' => $order->id, 'status' => $order->status],
        'title'  => 'Order Update'
        ];
    }
}
