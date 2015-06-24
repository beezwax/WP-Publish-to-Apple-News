<?php
namespace Exporter\Components;

/**
 * A caption.
 *
 * @since 0.2.0
 */
class Caption extends Component {

	public static function node_matches( $node ) {
		// Returning an array tells the factory we matched several components.
		// Return them as shortname => node
		//
		// FIXME: Returning different types of data
		if ( 'figure' == $node->nodeName ) {
			$image   = self::node_find_by_tagname( $node, 'img' );
			$caption = self::node_find_by_tagname( $node, 'figcaption' );

			return array(
				'img'     => $image,
				'caption' =>	$caption,
		 	);
		}

		// Matched only a figcaption
		if ( 'figcaption' == $node->nodeName ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		// Remove initial and trailing tags: <video><p>...</p></video>
		if ( ! preg_match( '#<.+?>(.*?)</.+?>#m', $text, $match ) ) {
			return null;
		}

		$text = $match[1];

		// Save video into bundle
		$this->json = array(
			'role' => 'caption',
			'text' => $text,
		);
	}

}

