<?php

namespace App\Helper;

class LaravelFlashSessionHelper {
	/**
	 * Function sets the flash message with type
	 *
	 * @param string $message [contains message to be displayed]
	 * @param string $type [contains type of message]
	 */
	public static function setFlashMessage( $message, $type = 'success' ) {
		if ( session()->has( 'flashMessages' ) ) {
			$data = session( 'flashMessages' );
		} else {
			$data = [
				'success' => [],
				'info'    => [],
				'error'   => [],
				'warning' => [],
			];
		}

		$data[ $type ][] = $message;

		session()->flash( 'flashMessages', $data );
	}
}