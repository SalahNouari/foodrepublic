<?php

namespace App\Events;
use App\Vendor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vendor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('timer');
        // return new PrivateChannel('channel-name');
    }
    public function broadcastAs()
    {
        return 'timer_event';
    }
    public function broadcastWith()
    {
        $id = $this->vendor->id;
        $img = $this->vendor->image;
        $name = $this->vendor->name;
       
        return [
            'id' => $id,
            'image' => $img,
            'name' => $name,
        ];
    }
}