<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Libraries\Firebase;
use App\Libraries\Push;
class NotificationListener
{
  
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        
		// $datas = json_decode( $event->notification->payload );

		$receiver               = $event->notification['receiver_user'];
		$notification_payload   = $event->notification['payload'];
		$notification_title     = $event->notification['title'];
		$notification_message   = $event->notification['message'];
		$notification_push_type = $event->notification['push_type'];

		try {

			$receiver_id = $receiver;

			$firebase = new Firebase();
			$push     = new Push();

			// optional payload
			$payload = $notification_payload;

			$title = $notification_title ?? '';

			// notification message
			$message = $notification_message ?? '';

			// push type - single user / topic
			$push_type = $notification_push_type ?? '';

		
			$push->setTitle( $title );
			$push->setMessage( $message );
			$push->setPayload( $payload );


			$json     = '';
			$response = '';

			if ( $push_type === 'topic' ) {
				$json     = $push->getPush();
				$response = $firebase->sendToTopic( 'global', $json );
			} else if ( $push_type === 'individual' ) {
				$json     = $push->getPush();
				$regId    = $receiver_id ?? '';
				if (isset($payload->url)) {
				
				switch ($payload->url) {
				case '/adminorder':
						$env = 'vendor';
						break;
				case '/delivery':
						$env = 'delivery';
						break;
					default:
						$env = 'user';
					break;
				}
			} else {
				$env = 'user';
				}
			
				$response = $firebase->send( $regId, $json, $env );

				return response()->json( [
                    'response' => $response,
                    'oda' => $json
				] );
			} else if ( $push_type === 'multiple' ) {
				$json     = $push->getPush();
				$regId    = $receiver_id ?? '';
				$response = $firebase->sendMultiple( $regId, $json );

				return response()->json( [
                    'response' => $response,
                    'oda' => $json
				] );
			}
			
		} catch ( \Exception $ex ) {
			return response()->json( [
				'error'   => true,
				'message' => $ex->getMessage()
			] );
		}
    }
}
