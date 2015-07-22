<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section_Advanced extends Admin_Settings_Section {

	protected $name = 'Advanced Settings';
	protected $slug = 'advanced-options';

	protected $settings = array(
		'body_line_height' => array(
			'label'   => 'Body Line Height',
			'type'    => 'float',
		),
		'pullquote_line_height' => array(
			'label'   => 'Pullquote Line Height',
			'type'    => 'float',
		),
		'header_line_height' => array(
			'label'   => 'Heading Line Height',
			'type'    => 'float',
		),
 	);

	protected $groups = array(
		'line_heights' => array(
			'label'       => 'Line Heights',
			'settings'    => array( 'body_line_height', 'pullquote_line_height', 'header_line_height' ),
		),
	);

	public function print_section_info() {
		echo 'Settings for advanced users and designers. These are optional and have good defaults, so don\'t worry if you don\'t quite understand them.';
	}

}
