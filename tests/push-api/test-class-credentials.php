<?php

require_once __DIR__ . '/../../includes/push-api/class-credentials.php';

use \Push_API\Credentials as Credentials;

class Credentials_Test extends WP_UnitTestCase {

	public function setup() {
		$this->credentials = new Credentials( 'foo', 'bar' );
	}

	public function testGetsValues() {
		$this->assertEquals(
			'foo',
			$this->credentials->key()
		);

		$this->assertEquals(
			'bar',
			$this->credentials->secret()
		);
	}

}

