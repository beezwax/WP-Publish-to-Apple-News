<?php
namespace Exporter\Components;

/**
 * Represents an Article Format Advertisment. It gets generated automatically
 * so not much to do here but define the static JSON.
 *
 * @since 0.4.0
 */
class Advertisment extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'banner_advertisement',
			'bannerType' => 'standard',
		);

		$this->set_default_layout();
	}

	private function set_default_layout() {
		$this->json[ 'layout' ] = 'advertisment-layout';
		$this->register_layout( 'advertisment-layout', array(
			'margin' => array(
				'bottom' => 40,
			),
		) );
	}

}

