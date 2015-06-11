<?php
namespace Exporter\Components;

/**
 * An image gallery is just a container with 'gallery' class and some images
 * inside. The container should be a div, but can be anything as long as it has
 * a 'gallery' class.
 *
 * @since 0.0.0
 */
class Gallery extends Component {

	protected function build( $text ) {
		preg_match_all( '/src="([^"]+)"/', $text, $matches );
		$urls  = $matches[1];
		$items = array();

		foreach ( $urls as $url ) {
			// Save to bundle
			$filename = basename( $url );
			$this->bundle_source( $filename, $url );

			// Collect into to items array
			$items[] = array(
				'URL' => 'bundle://' . $filename,
			);
		}

		$this->json = array(
			// TODO: Depending on the configuration, this could also be 'mosaic'.
			'role' => 'gallery',
			'items' => $items,
		);
	}

}

