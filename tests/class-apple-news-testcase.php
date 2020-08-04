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
	 * Contains an instance of the Apple_Exporter\Exporter_Content class for use in tests.
	 *
	 * @var Apple_Exporter\Exporter_Content
	 */
	protected $content;

	/**
	 * Contains an instance of the Apple_Exporter\Exporter_Content_Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Exporter_Content_Settings
	 */
	protected $content_settings;

	/**
	 * Contains an instance of the Apple_Exporter\Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Settings
	 */
	protected $settings;

	/**
	 * Contains an instance of the Apple_Exporter\Theme class for use in tests.
	 *
	 * @var Apple_Exporter\Theme
	 */
	protected $theme;

	/**
	 * A function containing operations to be run before each test function.
	 *
	 * @access public
	 */
	public function setUp() {
		parent::setup();

		// Create some dummy content and save it for future use.
		$this->content = new Apple_Exporter\Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>'
		);

		// Create a new instance of the Settings object and save it for future use.
		$this->settings = new Apple_Exporter\Settings();

		// Create a new instance of the Exporter_Content_Settings object and save it for future use.
		$this->content_settings = new Apple_Exporter\Exporter_Content_Settings();

		// Create a new theme and save it for future use.
		$this->theme = new Apple_Exporter\Theme();
		$this->theme->save();
		$this->theme->set_active();
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
