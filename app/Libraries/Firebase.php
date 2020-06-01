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
	public function send( $to, $message ) {

		$fields = array(
			"to"   => $to,
			"notification" =>array(
				"title"=> $message["title"],
				"body"=> $message["body"],
				"sound"=> "default"
			),
			"data" => $message['data']
		);
		if (isset($message['data']['delivery'])) {
			$vendor = Auth::user()->vendor;
			$fields = array(
				"to"   => $message['data']['delivery'],
				"notification" =>array(
					"title"=> 'New Order!!',
					"body"=> $vendor->name,
					"sound"=> "default"
				),
				"data" => $message['data']
			);
			$this->sendPushNotification( $fields );
		}

		return $this->sendPushNotification( $fields );
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

		return $this->sendPushNotification( $fields );
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

		return $this->sendPushNotification( $fields );
	}

	/**
	 * POST request to firebase servers
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	private function sendPushNotification( $fields ) {

		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';

		$client = new Client();

		$result = $client->post( $url, [
			'json'    =>
				$fields
			,
			'headers' => [
				'Authorization' => 'key=' . env( 'FCM_LEGACY_KEY' ),
				'Content-Type'  => 'application/json',
			],
		] );

		return json_decode( $result->getBody(), true );

	}
}