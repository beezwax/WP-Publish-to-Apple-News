<?php
namespace Exporter\Builders;

use \Exporter\Exporter as Exporter;

/**
 * @since 0.4.0
 */
class Article_Layout {

	private $settings;

	function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function to_array() {
		return array(
			'columns' => Exporter::LAYOUT_COLUMNS,
			'width'   => Exporter::LAYOUT_WIDTH,
			'margin'  => $this->get_setting( 'layout_margin' ),  // Defaults to 30
			'gutter'  => $this->get_setting( 'layout_gutter' ),  // Defaults to 20
		);
	}

	private function get_setting( $name ) {
		return $this->settings->get( $name );
	}

}
