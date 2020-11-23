<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryManNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

      
    public $notification;
    /**
     * Create a new event instance.
     *
     * @return void
     */
 
    public function __construct($token, $message, $title)
    {
        $this->notification = [
        'receiver_user'=> $token,
		'message' => $message,
		'push_type' => 'individual',
		'payload' => ['url' => '/delivery'],
        'title'  => $title
        ];
    }

   
}
