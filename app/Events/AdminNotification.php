<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $notification;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($token, $message, $vendor, $title)
    {
        $this->notification = [
            'receiver_user'=> 'e5A9nvLnRo6fA85hAPJv64:APA91bEYD2hsMPxWrh3mfD7Uuvnc-6HhihVLQHWKbvWrTGhKNy6m7qtwrYELj0KPsN9s9BYTWHMuN7bdznIQG1j-wpZ4Ew5AJjrp5VL_oL8yEA9boa7PlLNtcoxmRzPraQ1QTN7KEBXd',
            'message' => $message,
            'push_type' => 'individual',
            'payload' => ['url' => '/adminorder', 'vendor' => $vendor],
            'title'  =>  $title
            ];
    }


}
