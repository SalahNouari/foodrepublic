<?php

namespace App\Events;
use App\Order;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('orders');
        // return new PrivateChannel('channel-name');
    }
    public function broadcastAs()
    {
        return 'order_event';
    }
    public function broadcastWith()
    {
        $ord = $this->order()->select('id', 'vendor_id', 'address_id', 'delivery_status', 'created_at', 'updated_at', 'status')
        ->with(['address' => function ($query) {
            $query->select('id', 'lat', 'lng', 'name');
        },'vendor' => function ($query) {
            $query->select('id','name', 'address', 'lat', 'lng');
        }])
        ->get();
       
        return [
            'order' => $ord
        ];
    }
}
