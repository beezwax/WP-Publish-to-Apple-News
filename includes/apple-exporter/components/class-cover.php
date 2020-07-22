<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Cover class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

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

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$this->register_spec(
			'json',
			__( 'JSON', 'apple-news' ),
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'   => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL'    => '#url#',
					),
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8,
				),
			)
		);

		$this->register_spec(
			'jsonWithCaption',
			__( 'JSON with Caption', 'apple-news' ),
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'    => 'photo',
						'layout'  => 'headerPhotoLayout',
						'URL'     => '#url#',
						'caption' => '#caption#',
					),
					array(
						'role'      => 'caption',
						'text'      => '#caption#',
						'format'    => 'html',
						'textStyle' => array(
							'textAlignment' => '#text_alignment#',
							'fontName'      => '#caption_font#',
							'fontSize'      => '#caption_size#',
							'tracking'      => '#caption_tracking#',
							'lineHeight'    => '#caption_line_height#',
							'textColor'     => '#caption_color#',
						),
					),
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8,
				),
			)
		);

		$this->register_spec(
			'headerPhotoLayout',
			__( 'Layout', 'apple-news' ),
			array(
				'ignoreDocumentMargin' => true,
				'columnStart'          => 0,
				'columnSpan'           => '#layout_columns#',
			)
		);

		$this->register_spec(
			'headerBelowTextPhotoLayout',
			__( 'Below Text Layout', 'apple-news' ),
			array(
				'ignoreDocumentMargin' => true,
				'columnStart'          => 0,
				'columnSpan'           => '#layout_columns#',
				'margin'               => array(
					'top'    => 30,
					'bottom' => 0,
				),
			)
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML for the cover image. Can be a bare URL or an img or figure tag.
	 * @access protected
	 */
	protected function build( $html ) {

		// Determine if we were given a bare URL or HTML.
		$url = filter_var( $html, FILTER_VALIDATE_URL )
			? $html
			: self::url_from_src( $html );

		// Bundle the source, if necessary.
		$url = trim( $this->maybe_bundle_source( $url ) );

		// If we failed to get a URL, bail out.
		if ( empty( $url ) ) {
			return;
		}

		// Fork for caption vs. not.
		if ( preg_match( '/<(?:figcaption|p)[^>]+class=[\'"][^\'"]*wp-caption-text[^\'"]*[\'"][^>]*>(.+?)(?:<\/figcaption|<\/p>)/', $html, $matches ) ) {
			$this->register_json(
				'jsonWithCaption',
				array(
					'#caption#' => $matches[1],
					'#url#'     => $url,
				)
			);
		} else {
			$this->register_json(
				'json',
				array(
					'#url#' => $url,
				)
			);
		}

		$this->set_default_layout();
	}

	/**
	 * Set the default layout for the component.
	 *
	 * @access private
	 */
	private function set_default_layout() {

		// Get information about the currently loaded theme.
		$theme = \Apple_Exporter\Theme::get_used();

		$this->register_layout(
			'headerPhotoLayout',
			'headerPhotoLayout',
			array(
				'#layout_columns#' => $theme->get_layout_columns(),
			)
		);

		$this->register_layout(
			'headerBelowTextPhotoLayout',
			'headerBelowTextPhotoLayout',
			array(
				'#layout_columns#' => $theme->get_layout_columns(),
			)
		);
	}
}
