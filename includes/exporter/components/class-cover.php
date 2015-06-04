<?php
namespace Exporter\Components;

class Cover extends Component {

	protected function build( $url ) {
		$filename = array_pop( explode( '/', $url ) );
		// Save image into bundle
		$this->workspace->write_tmp_file( $filename, file_get_contents( $url ) );

		$this->json = array(
			'role' => 'container',
			'layout' => 'headerContainerLayout',
			'style' => array(
				'fill' => array(
					'type' => 'image',
					'URL' => 'bundle://' . $filename,
					'fillMode' => 'cover',
				),
			),
		);
	}

	// TODO: Maybe something like this? That way components can require and
	// create their own layouts if needed.
	// function __construct() {
	//		Component_Factory::register_layout( 'headerContainerLayout', ... );
	//		// OR
	//		$exporter->register_layout( 'headerContainerLayout', ... );
	//		// OR
	//		Layout_Factory::register_layout( 'headerContainerLayout', ... );
	// }

}

