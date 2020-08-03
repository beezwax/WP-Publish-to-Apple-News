<?php
/**
 * Publish to Apple News tests: Apple_News_Testcase class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A base class for Apple News tests.
 *
 * @package Apple_News
 */
abstract class Apple_News_Testcase extends WP_UnitTestCase {

	/**
	 * Contains an instance of the Apple_Exporter\Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Settings
	 */
	protected $settings;

	/**
	 * A function containing operations to be run before each test function.
	 *
	 * @access public
	 */
	public function setUp() {
		parent::setup();
		$this->settings = new Apple_Exporter\Settings();
	}

	/**
	 * A helper function that generates JSON for a given post ID.
	 *
	 * @param int $post_id The for which to perform the export.
	 *
	 * @return array The JSON for the post, converted to an associative array.
	 */
	protected function get_json_for_post( $post_id ) {
		$export = new Apple_Actions\Index\Export(
			$this->settings,
			$post_id,
			Admin_Apple_Sections::get_sections_for_post( $post_id )
		);

		return json_decode( $export->perform(), true );
	}
}
