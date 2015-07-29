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
			'label'    => 'Body Line Height',
			'type'     => 'float',
			'sanitize' => 'sanitize_float',
		),
		'pullquote_line_height' => array(
			'label'   => 'Pull quote Line Height',
			'type'    => 'float',
			'sanitize' => 'sanitize_float',
		),
		'header_line_height' => array(
			'label'   => 'Heading Line Height',
			'type'    => 'float',
			'sanitize' => 'sanitize_float',
		),
 	);

	public function sanitize_float( $val ) {
		return floatval( $val );
	}

	protected $groups = array(
		'line_heights' => array(
			'label'       => 'Line Heights',
			'settings'    => array( 'body_line_height', 'pullquote_line_height', 'header_line_height' ),
		),
	);

	public function print_section_info() {
		echo 'Delete values to restore defaults.';
	}

}
