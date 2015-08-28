<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section_Advanced extends Admin_Settings_Section {

	/**
	 * Name of the advanced settings section.
	 *
	 * @var string
	 * @access protected
	 */
	protected $name = 'Advanced Settings';

	/**
	 * Slug of the advanced settings section.
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug = 'advanced-options';

	/**
	 * Advanced settings.
	 *
	 * @var array
	 * @access protected
	 */
	protected $settings = array(
		'body_line_height' => array(
			'label'    => 'Body Line Height',
			'type'     => 'float',
			'sanitize' => 'floatval',
		),
		'pullquote_line_height' => array(
			'label'   => 'Pull quote Line Height',
			'type'    => 'float',
			'sanitize' => 'floatval',
		),
		'header_line_height' => array(
			'label'   => 'Heading Line Height',
			'type'    => 'float',
			'sanitize' => 'floatval',
		),
 	);

 	/**
	 * Groups for advanced settings.
	 *
	 * @var array
	 * @access protected
	 */
 	protected $groups = array(
		'line_heights' => array(
			'label'       => 'Line Heights',
			'settings'    => array( 'body_line_height', 'pullquote_line_height', 'header_line_height' ),
		),
	);

	/**
	 * Prints section info.
	 *
	 * @access public
	 */
	public function print_section_info() {
		echo 'Delete values to restore defaults.';
	}
}
