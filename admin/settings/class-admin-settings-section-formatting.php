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
			'label'   => '',
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
			'label'   => '',
			'type'    => 'font',
		),
		'dropcap_color' => array(
			'label'   => 'Dropcap font color',
			'type'    => 'color',
		),
		'byline_font' => array(
			'label'   => '',
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
			'label'   => '',
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
			'label'   => '',
			'type'    => 'font',
		),
		'pullquote_size' => array(
			'label'   => 'Pull quote font size',
			'type'    => 'integer',
		),
		'pullquote_color' => array(
			'label'   => 'Pull quote color',
			'type'    => 'color',
		),
		'pullquote_transform' => array(
			'label'   => 'Pull quote transformation',
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

	protected $groups = array(
		'layout' => array(
			'label'       => 'Layout Spacing',
			'description' => 'The spacing for the base layout of the exported articles',
			'settings'    => array( 'layout_margin', 'layout_gutter' ),
		),
		'body' => array(
			'label'       => 'Body',
			'settings'    => array( 'body_font', 'body_size', 'body_color', 'body_link_color', 'body_orientation' ),
		),
		'dropcap' => array(
			'label'       => 'Dropcap',
			'settings'    => array( 'dropcap_font', 'initial_dropcap', 'dropcap_color' ),
		),
		'byline' => array(
			'label'       => 'Byline',
			'description' => 'The byline displays the article\'s author and date',
			'settings'    => array( 'byline_font', 'byline_size', 'byline_color' ),
		),
		'headings' => array(
			'label'       => 'Headings',
			'settings'    => array( 'header_font', 'header_color', 'header1_size',
			  'header2_size', 'header3_size', 'header4_size', 'header4_size',
			  'header5_size', 'header6_size' ),
		),
		'pullquote' => array(
			'label'       => 'Pull quote',
			'description' => 'Articles can have an optional <a href="https://en.wikipedia.org/wiki/Pull_quote">Pull quote</a>.',
			'settings'    => array( 'pullquote_font', 'pullquote_size', 'pullquote_color', 'pullquote_transform' ),
		),
		'gallery' => array(
			'label'       => 'Gallery',
			'description' => 'Can either be a standard gallery, or mosaic.',
			'settings'    => array( 'gallery_type' ),
		),
		'advertisement' => array(
			'label'       => 'Advertisement',
			'settings'    => array( 'enable_advertisement' ),
		),
	);

	public function print_section_info() {
		echo 'Configuration on the look and feel of the generated articles';
	}

}
