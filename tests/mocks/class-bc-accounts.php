<?php
/**
 * Apple News Tests Mocks: BC_Accounts class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A mock for the BC_Accounts class from the Brightcove Video Connect plugin.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class BC_Accounts {

	/**
	 * Mocks the functionality of BC_Accounts::get_account_id.
	 *
	 * Returns the currently "set" account ID, which will always be 12345678980.
	 *
	 * @return string The account ID.
	 */
	public function get_account_id() {
		return '1234567890';
	}

	/**
	 * Mocks the functionality of BC_Accounts::set_current_account_by_id.
	 *
	 * @param string $account_id The account ID to set.
	 *
	 * @return array An array containing the "new" account object.
	 */
	public function set_current_account_by_id( $account_id ) {
		return [
			'account_id'    => $account_id,
			'account_name'  => 'Test Account Name',
			'client_id'     => 'abcd1234-ef56-ab78-cd90-efabcd123456',
			'client_secret' => 'AbCdEfGhIjKlMnOpQrStUvWxYz12345678-AbCdEfGhIjKlMnOpQrStUvWxYz0_AbCdE-AbCdEfG_AbCdE_AbC',
			'hash'          => 'abcdef0123456789',
			'set_default'   => 'default',
		];
	}
}
