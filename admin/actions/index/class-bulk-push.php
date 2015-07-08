<?php

namespace Actions\Index;

require_once __DIR__ . '/../class-action.php';

use Actions\Action as Action;
use Push_API\API as API;
use Push_API\Credentials as Credentials;

class Bulk_Push extends Action {

	const API_ENDPOINT = 'https://u48r14.digitalhub.com';

	private $ids;

	function __construct( $settings, $ids ) {
		parent::__construct( $settings );
		$this->ids = $ids;
	}

	/**
	 * Must be implemented when extending Action. Performs the action and returns
	 * errors if any, null otherwise.
	 *
	 * @since 0.6.0
	 */
	public function perform() {
		$errors = array();

		if ( empty( $this->ids ) ) {
			$errors[] = 'You did not select any articles.';
			return $errors;
		}

		foreach( $this->ids as $id ) {
			$error = $this->push( $id );
			if( ! is_null( $error ) ) {
				$errors[] = $error;
			}
		}

		return $errors;
	}

	private function push( $id ) {
		$action = new Push( $this->settings, $id );
		return $action->perform();
	}

}

