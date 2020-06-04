<?php

namespace App\Http\Controllers;

use App\Libraries\Firebase;
use App\Libraries\Push;
use Illuminate\Http\Request;


class NotificationController extends Controller {
	public function notify(Request $request) {

		$datas = json_decode( $request->payload );

		$sender                 = $request->sender_user;
		$receiver               = $request->receiver_user;
		$notification_payload   = $datas;
		$notification_title     = $request->title;
		$notification_message   = $request->message;
		$notification_push_type = $request->push_type;

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


// {"data":"{\"payload\":{\"a\":\"b\",\"c\":\"d\",\"e\":\"f\"},
// \"timestamp\":\"2020-04-10 13:46:14\"}",
// "notification":"{\"title\":\"hello from postman\",
// 	\"body\":\"hi its me post me.\"}"}}

// Push received: {"id":"0:1586524409541792%23266720f9fd7ecd","data":
// 	{"body":"hi its me post me.","data":"{\"payload\":{\"a\":\"b\",\"c\":\"d\",\"e\":\"f\"},
// \"timestamp\":\"2020-04-10 

// Msg: Push received: {"id":"0:1586524493857694%2326672023266720",
// 	"data":{},"title":"Deals!!!",
// "body":"Get hot meals from close vendors now!!"}