<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\HTML class
 *
 * Contains a class which is used to filter raw HTML into Apple News HTML format.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 1.2.1
 */

namespace Apple_Exporter;

/**
 * A class that filters raw HTML into Apple News HTML format.
 *
 * @since 1.2.1
 */
class HTML {

	/**
	 * An array of allowed HTML tags for Apple News formatted HTML.
	 *
	 * @access private
	 * @var array
	 */
	private array $allowed_html = [
		'a'          => [
			'href' => true,
			'id'   => true,
		],
		'aside'      => [ 'id' => true ],
		'b'          => [ 'id' => true ],
		'blockquote' => [ 'id' => true ],
		'br'         => [ 'id' => true ],
		'caption'    => [ 'id' => true ],
		'cite'       => [ 'id' => true ],
		'code'       => [ 'id' => true ],
		'del'        => [ 'id' => true ],
		'em'         => [ 'id' => true ],
		'footer'     => [ 'id' => true ],
		'i'          => [ 'id' => true ],
		'li'         => [ 'id' => true ],
		'ol'         => [ 'id' => true ],
		'p'          => [ 'id' => true ],
		'pre'        => [ 'id' => true ],
		's'          => [ 'id' => true ],
		'samp'       => [ 'id' => true ],
		'strong'     => [ 'id' => true ],
		'sub'        => [ 'id' => true ],
		'sup'        => [ 'id' => true ],
		'table'      => [ 'id' => true ],
		'td'         => [ 'id' => true ],
		'th'         => [ 'id' => true ],
		'tr'         => [ 'id' => true ],
		'tbody'      => [ 'id' => true ],
		'thead'      => [ 'id' => true ],
		'tfoot'      => [ 'id' => true ],
		'ul'         => [ 'id' => true ],
	];

	/**
	 * Formats a raw HTML string as Apple News format HTML.
	 *
	 * @param string $html The HTML to format.
	 *
	 * @access public
	 * @return string The formatted HTML.
	 */
	public function format( $html ) {

		// Since wp_kses has an issue with some <script> tags, proactively strip them.
		$html = preg_replace( '/<script[^>]*?>.*?<\/script>/', '', $html );

		// Strip out all tags and attributes other than what is allowed.
		$html = wp_kses( $html, $this->allowed_html );

		// Remove any empty tags.
		$html = preg_replace( '/<([a-z0-9]+)[^>]*>\s*<\/\1>/', '', $html );

		return $html;
	}
}
