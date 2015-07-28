<?php
namespace Exporter\Components;

/**
 * An image gallery is just a container with 'gallery' class and some images
 * inside. The container should be a div, but can be anything as long as it has
 * a 'gallery' class.
 *
 * @since 0.2.0
 */
class Gallery extends Component {

	public static function node_matches( $node ) {
		if ( self::node_has_class( $node, 'gallery' ) ) {
			return $node;
		}

		return null;
	}

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
			'role'   => $this->get_setting( 'gallery_type' ),
			'items'  => $items,
		);

		$this->set_layout();
	}

	private function set_layout() {
		$this->json['layout'] = 'gallery-layout';
		$this->register_full_width_layout( 'gallery-layout', array(
			'margin' => array( 'top' => 25, 'bottom' => 25 )
		) );
	}

}

