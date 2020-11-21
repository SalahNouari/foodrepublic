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

class VendorEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vendor, $time, $area;
    /**
     * Create a new event instance.
     *
     * @return void
     */
 
    public function __construct(Vendor $vendor, $time, $area, $d_id)
    {
        $this->time = $time;
        $this->vendor = $vendor;
        $this->d_id = $d_id;
        $this->area = $area;
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
        return 'timer_event_'.$this->area;;
    }
    public function broadcastWith()
    {
        $id = $this->vendor->id;
        $img = $this->vendor->image;
        $d_id = $this->d_id;
        $name = $this->vendor->name;
       
        return [
            'id' => $id,
            'image' => $img,
            'name' => $name,
            'd_id' => $d_id,
            'end_time' => $this->time
        ];
    }
}
