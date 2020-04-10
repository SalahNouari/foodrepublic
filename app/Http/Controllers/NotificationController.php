<?php

namespace App\Http\Controllers;

use App\Libraries\Firebase;
use App\Libraries\Push;
use Illuminate\Http\Request;


class NotificationController extends Controller {
	public function notify(Request $data) {

		$datas = json_decode( $data->payload );

		$sender                 = $data->sender_user;
		$receiver               = $data->receiver_user;
		$notification_payload   = $datas;
		$notification_title     = $data->title;
		$notification_message   = $data->message;
		$notification_push_type = $data->push_type;

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
				$response = $firebase->send( $regId, $json );

				return response()->json( [
					'response' => $response
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