<?php
/**
 * Publish to Apple News tests: Apple_News_Credentials_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Push_API\Credentials;

/**
 * A class to test the behavior of the Apple_Push_API\Credentials class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Credentials_Test extends Apple_News_Testcase {

	/**
	 * Tests the behavior of the getters on the Credentials class.
	 */
	public function test_gets_values() {
		$credentials = new Credentials( 'foo', 'bar' );
		$this->assertEquals( 'foo', $credentials->key() );
		$this->assertEquals( 'bar', $credentials->secret() );
	}
}
