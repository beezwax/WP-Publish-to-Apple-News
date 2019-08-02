<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\Components\Flickr class
 *
 * Contains a class which is used to transform Flickr embeds into Apple News format.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 0.2.0
 */

namespace Apple_Exporter\Components;

/**
 * A class to transform an Flickr embed into an Flickr Apple News component.
 *
 * @since 0.2.0
 */
class Flickr extends Component {

	/**
	 * Look for node matches for this component.
	 *
	 * @param \DOMElement $node The node to examine for matches.
	 *
	 * @access public
	 * @return \DOMElement|null The node on success, or null on no match.
	 */
	public static function node_matches( $node ) {

		// Match the src attribute against a flickr regex
		if ( 1 === $node->nodeType && false !== strpos( $node->getAttribute('class'), 'is-provider-flickr' ) ) {
			return $node;
		}

		return null;
	}

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$this->register_spec(
			'flickr-json',
			__( 'Flickr JSON', 'apple-news' ),
			array(
				'role' => 'container',
				'components' => '#components#',
			)
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 *
	 * @access protected
	 */
	protected function build( $html ) {

		if ( preg_match( '#https?://live.staticflickr.com/[^"]+#', $html, $matches ) ) {
			$url = $matches[0];
		}

		// Ensure we got a URL.
		if ( empty( $url ) ) {
			return;
		}

		// Build the caption
		$caption = '';
		$hide_article_caption = false;
		if ( preg_match( '#figcaption>(.*?)</fig#', $html, $matches ) ) {
			$caption = $matches[1];
		} else if ( preg_match( '#img.*?alt="(.*?)"#', $html, $matches ) ) {
			$caption = $matches[1];
			$hide_article_caption = true;
		}

		// Check if embed is for an album
		$is_album_embed = false;
		if ( preg_match( '#(flickr-embed).*?href="(.*?)".*?title="(.*?)"#', $html, $matches ) ) {
			$is_album_embed = ! empty( $matches[1] );
			$album_url = $matches[2];
			$album_title = $matches[3];
		}

		if ( $is_album_embed ) {
			if ( empty( $caption ) || $hide_article_caption ) {
					$caption = '';
			} else {
					$caption .= '<br>';
			}
			$caption .= '<a href="' . $album_url . '">' . $album_title . '</a>';
		}

		$this->register_json(
			'flickr-json',
			array(
				'#components#' => array(
					[
						'role' => 'photo',
						'URL' => $url,
						'caption' => [
							'text' => $caption,
							'format' => 'html'
						]
					],
					[
						'role' => 'caption',
						'text' => $caption,
						'format' => 'html',
						'hidden' => ! $is_album_embed && ( $hide_article_caption || empty( $caption ) )
					]
				)
			)
		);
	}
}
