<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section_Formatting extends Admin_Settings_Section {

	protected $name = 'Formatting';
	protected $slug = 'formatting-options';

	protected $settings = array(
		'layout_margin' => array(
			'label'   => 'Layout margin',
			'type'    => 'integer',
		),
		'layout_gutter' => array(
			'label'   => 'Layout gutter',
			'type'    => 'integer',
		),
		'body_font' => array(
			'label'   => 'Body font',
			'type'    => 'font',
		),
		'body_size' => array(
			'label'   => 'Body font size',
			'type'    => 'integer',
		),
		'body_color' => array(
			'label'   => 'Body font color',
			'type'    => 'color',
		),
		'body_link_color' => array(
			'label'   => 'Body font hyperlink color',
			'type'    => 'color',
		),
		'body_orientation' => array(
			'label'   => 'Body alignment',
			'type'    => array( 'left', 'center', 'right' ),
		),
		'initial_dropcap' => array(
			'label'   => 'Use initial dropcap',
			'type'    => array( 'yes', 'no' ),
		),
		'dropcap_font' => array(
			'label'   => 'Dropcap font',
			'type'    => 'font',
		),
		'dropcap_color' => array(
			'label'   => 'Dropcap font color',
			'type'    => 'color',
		),
		'byline_font' => array(
			'label'   => 'Byline font',
			'type'    => 'font',
		),
		'byline_size' => array(
			'label'   => 'Byline font size',
			'type'    => 'integer',
		),
		'byline_color' => array(
			'label'   => 'Byline font color',
			'type'    => 'color',
		),
		'header_font' => array(
			'label'   => 'Header font',
			'type'    => 'font',
		),
		'header_color' => array(
			'label'   => 'Header font color',
			'type'    => 'color',
		),
		'header1_size' => array(
			'label'   => 'Header 1 font size',
			'type'    => 'integer',
		),
		'header2_size' => array(
			'label'   => 'Header 2 font size',
			'type'    => 'integer',
		),
		'header3_size' => array(
			'label'   => 'Header 3 font size',
			'type'    => 'integer',
		),
		'header4_size' => array(
			'label'   => 'Header 4 font size',
			'type'    => 'integer',
		),
		'header5_size' => array(
			'label'   => 'Header 5 font size',
			'type'    => 'integer',
		),
		'header6_size' => array(
			'label'   => 'Header 6 font size',
			'type'    => 'integer',
		),
		'pullquote_font' => array(
			'label'   => 'Pullquote font',
			'type'    => 'font',
		),
		'pullquote_size' => array(
			'label'   => 'Pullquote font size',
			'type'    => 'integer',
		),
		'pullquote_color' => array(
			'label'   => 'Pullquote color',
			'type'    => 'color',
		),
		'pullquote_transform' => array(
			'label'   => 'Pullquote transformation',
			'type'    => array( 'none', 'uppercase' ),
		),
		'gallery_type' => array(
			'label'   => 'Gallery type',
			'type'    => array( 'gallery', 'mosaic' ),
		),
		'enable_advertisement' => array(
			'label'   => 'Enable advertisement',
			'type'    => array( 'yes', 'no' ),
		),
 	);

	public function print_section_info() {
		echo 'Configuration on the look and feel of the generated articles';
	}

}
