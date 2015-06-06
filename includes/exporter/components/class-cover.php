<?php
namespace Exporter\Components;

class Cover extends Component {

	protected function build( $url ) {
		$filename = basename( $url );

		// Save image into bundle
		$content = $this->get_file_contents( $url );
		$this->write_to_workspace( $filename, $content );

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

