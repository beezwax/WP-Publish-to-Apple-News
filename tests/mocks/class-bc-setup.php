<?php
/**
 * Apple News Tests Mocks: BC_Setup class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A mock for the BC_Setup class from the Brightcove Video Connect plugin.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class BC_Setup {
	/**
	 * Mocks the setup actions that happen on init that are relevant for testing this plugin.
	 */
	public function action_init() {
		global $bc_accounts;

		require_once __DIR__ . '/class-bc-accounts.php';
		require_once __DIR__ . '/class-bc-cms-api.php';

		$bc_accounts = new BC_Accounts();
	}
}
