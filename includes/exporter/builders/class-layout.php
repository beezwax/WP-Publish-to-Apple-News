<?php
namespace Exporter\Builders;

use \Exporter\Exporter as Exporter;

/**
 * Manage the article layout.
 *
 * @since 0.4.0
 */
class Layout extends Builder {

	protected function build() {
		return array(
			'columns' => $this->get_setting( 'layout_columns' ),
			'width'   => $this->get_setting( 'layout_width' ),
			'margin'  => $this->get_setting( 'layout_margin' ),  // Defaults to 30
			'gutter'  => $this->get_setting( 'layout_gutter' ),  // Defaults to 20
		);
	}

}
