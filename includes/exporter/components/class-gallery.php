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

	private function register_full_width_layout() {
		$base_layout = array(
			'margin' => array( 'top' => 50, 'bottom' => 30 ),
		);

		// If the body is centered, don't span the full width
		if ( 'center' == $this->get_setting( 'body_orientation' ) ) {
			$col_start = floor( ( $this->get_setting( 'layout_columns' ) - $this->get_setting( 'body_column_span' ) ) / 2 );
			$col_span  = $this->get_setting( 'body_column_span' );

			$base_layout['columnStart'] = $col_start;
			$base_layout['columnSpan']  = $col_span;
		}

		$this->json['layout'] = 'gallery-layout';
		$this->register_layout( 'gallery-layout', $base_layout  );
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

		$this->register_full_width_layout();
	}

}

