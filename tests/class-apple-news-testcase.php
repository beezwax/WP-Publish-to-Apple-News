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
	 * Contains an instance of the Apple_Exporter\Builders\Component_Styles class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Styles
	 */
	protected $component_styles;

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
	 * Contains an instance of the Apple_Exporter\Builders\Component_Layouts class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Layouts
	 */
	protected $layouts;

	/**
	 * Contains a Prophecy-wrapped instance of the Apple_Exporter\Workspace class.
	 *
	 * @var Prophecy\Prophecy\ObjectProphecy
	 */
	protected $prophecized_workspace;

	/**
	 * An instance of Prophet for use in tests.
	 *
	 * @var Prophecy\Prophet
	 */
	protected $prophet;

	/**
	 * Contains an instance of the Apple_Exporter\Settings class for use in tests.
	 *
	 * @var Apple_Exporter\Settings
	 */
	protected $settings;

	/**
	 * Contains an instance of the Apple_Exporter\Builders\Component_Text_Styles class for use in tests.
	 *
	 * @var Apple_Exporter\Builders\Component_Text_Styles
	 */
	protected $styles;

	/**
	 * Contains an instance of the Apple_Exporter\Theme class for use in tests.
	 *
	 * @var Apple_Exporter\Theme
	 */
	protected $theme;

	/**
	 * Contains an instance of the Apple_Exporter\Workspace class for use in tests.
	 *
	 * @var Apple_Exporter\Workspace
	 */
	protected $workspace;

	/**
	 * A function containing operations to be run before each test function.
	 *
	 * @access public
	 */
	public function setUp() {
		parent::setUp();

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

		// Create a new instance of Prophet for future use and create a prophecized workspace.
		$this->prophet               = new Prophecy\Prophet();
		$this->prophecized_workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );

		// Create a new theme and save it for future use.
		$this->theme = new Apple_Exporter\Theme();
		$this->theme->save();
		$this->theme->set_active();

		// Create styles for future use.
		$this->styles = new Apple_Exporter\Builders\Component_Text_Styles(
			$this->content,
			$this->content_settings
		);

		// Create layouts for future use.
		$this->layouts = new Apple_Exporter\Builders\Component_Layouts(
			$this->content,
			$this->content_settings
		);

		// Create component styles for future use.
		$this->component_styles = new Apple_Exporter\Builders\Component_Styles(
			$this->content,
			$this->content_settings
		);

		// Create a workspace for future use. Default it to use post ID 1, but this can be overridden at the test level.
		$this->set_workspace_post_id( 1 );
	}

	/**
	 * Actions to be run after every test.
	 *
	 * @access public
	 */
	public function tearDown() {
		$this->prophet->checkPredictions();
	}

	/**
	 * Runs create_upload_object using a test image and returns the image ID.
	 *
	 * @param int $parent Optional. The parent post ID. Defaults to no parent.
	 *
	 * @return int The post ID of the attachment image that was created.
	 */
	protected function get_new_attachment( $parent = 0 ) {
		return self::factory()->attachment->create_upload_object( __DIR__ . '/data/test-image.jpg', $parent );
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

	/**
	 * Given an array of theme settings, applies them to the currently active theme.
	 *
	 * @param array $settings The settings to apply to the theme.
	 */
	protected function set_theme_settings( $settings ) {
		$settings = wp_parse_args( $settings, $this->theme->all_settings() );
		$this->theme->load( $settings );
		$this->theme->save();
	}

	/**
	 * Sets the workspace post ID to the ID provided.
	 *
	 * @param int $post_id The post ID to set for the workspace.
	 */
	protected function set_workspace_post_id( $post_id ) {
		$this->workspace = new Apple_Exporter\Workspace( $post_id );
	}
}
