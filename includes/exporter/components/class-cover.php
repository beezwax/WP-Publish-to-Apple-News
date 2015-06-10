<?php
namespace Exporter\Components;

/**
 * A cover is optional and displayed at the very top of the article. It's
 * loaded from the Exporter_Content's cover attribute, if present.
 * This component does not need a node so no need to implement match_node.
 *
 * In a WordPress context, the Exporter_Content's cover attribute is a post's
 * thumbnail, a.k.a featured image.
 *
 * @since 0.0.0
 */
class Cover extends Component {

	protected function build( $url ) {
		$filename = basename( $url );

		// Save image into bundle
		$this->bundle_source( $filename, $url );

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

