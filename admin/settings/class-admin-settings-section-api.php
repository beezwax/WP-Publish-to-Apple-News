<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section_API extends Admin_Settings_Section {

	/**
	 * Name of the API settings section.
	 *
	 * @var string
	 * @access protected
	 */
	protected $name = 'API Settings';

	/**
	 * Slug of the API settings section.
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug = 'api-options';

	/**
	 * API settings.
	 *
	 * @var array
	 * @access protected
	 */
	protected $settings = array(
		'api_key' => array(
			'label'   => 'API Key',
			'type'    => 'string',
		),
		'api_secret' => array(
			'label'   => 'API Secret',
			'type'    => 'password',
		),
		'api_channel' => array(
			'label'   => 'API Channel',
			'type'    => 'string',
		),
		'api_autosync' => array(
			'label'   => 'Automatically publish to Apple News',
			'type'    => array( 'yes', 'no' ),
		),
 	);

	/**
	 * API groups.
	 *
	 * @var array
	 * @access protected
	 */
	protected $groups = array(
		'apple_news' => array(
			'label'       => 'Apple News API',
			'description' => 'All of these settings are required for publishing to Apple News',
			'settings'    => array( 'api_key', 'api_secret', 'api_channel', 'api_autosync' ),
		),
	);

	/**
	 * Prints section info.
	 *
	 * @access public
	 */
	public function print_section_info() {
		echo 'Enter your Apple News credentials below. See <a target="_blank"
			href="https://developer.apple.com/news-publisher/">the Apple News
			documentation</a> for detailed information.';
	}

}
