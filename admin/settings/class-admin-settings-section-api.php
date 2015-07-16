<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section_API extends Admin_Settings_Section {

	protected $name = 'API Settings';
	protected $slug = 'api-options';

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

	public function print_section_info() {
		echo 'Information about the Apple News API.';
	}

}
