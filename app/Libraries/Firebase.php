<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class Firebase {


	/**
	 * sending push message to single user by firebase reg id
	 *
	 * @param $to
	 * @param $message
	 *
	 * @return mixed
	 */
	public function send( $to, $message, $env ) {

		$fields = array(
			"to"   => $to,
			"notification" =>array(
				"title"=> $message["title"],
				"body"=> $message["body"],
				"sound"=> "default"
			),
			"data" => $message['data']
		);
		return $this->sendPushNotification( $fields, $env );
	}


	/**
	 * Sending message to a topic by topic name
	 *
	 * @param $to
	 * @param $message
	 *
	 * @return mixed
	 */
	public function sendToTopic( $to, $message ) {
		$fields = array(
			'to'   => '/topics/' . $to,
			'data' => $message,
		);

		return $this->sendPushNotification( $fields, 'user' );
	}


	/**
	 * Sending push message to multiple users by firebase registration ids
	 *
	 * @param $registration_ids
	 * @param $message
	 *
	 * @return mixed
	 */
	public function sendMultiple( $registration_ids, $message ) {
		$fields = array(
			'to'   => $registration_ids,
			'data' => $message,
		);

		return $this->sendPushNotification( $fields, 'delivery' );
	}

	/**
	 * POST request to firebase servers
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	private function sendPushNotification( $fields, $key ) {

		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';

		$client = new Client();
		switch ($key) {
			case 'user':
				$env = env( 'FCM_LEGACY_KEY_USER' );
				break;
			case 'vendor':
				$env = env( 'FCM_LEGACY_KEY_VENDOR' );
				break;
			case 'delivery':
				$env = env( 'FCM_LEGACY_KEY_DELIVERY' );
				break;
			
			default:
				$env = env( 'FCM_LEGACY_KEY_USER' );
				break;
		}
		$result = $client->post( $url, [
			'json'    =>
				$fields
			,
			'headers' => [
				'Authorization' => 'key=' . $env,
				'Content-Type'  => 'application/json',
			],
		] );

		return json_decode( $result->getBody(), true );

	}
}