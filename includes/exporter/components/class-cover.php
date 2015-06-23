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
 * @since 0.2.0
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

		$this->set_default_layout();
	}

	private function set_default_layout() {
		$this->register_layout( 'headerContainerLayout', array(
			'columnStart' => 0,
			'columnSpan' => $this->get_setting( 'layout_columns' ),
			'ignoreDocumentMargin' => true,
			'minimumHeight' => '50vh',
		) );
	}

}

