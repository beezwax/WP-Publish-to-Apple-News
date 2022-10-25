<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Byline class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * A byline normally describes who wrote the article, the date, etc.
 *
 * @since 0.2.0
 */
class Byline extends Component {

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$theme = \Apple_Exporter\Theme::get_used();

		$this->register_spec(
			'json',
			__( 'JSON', 'apple-news' ),
			[
				'role'   => 'byline',
				'text'   => '#text#',
				'format' => 'html',
			]
		);

		// Byline style conditional.
		$byline_conditional = [];

		if ( ! empty( $theme->get_value( 'byline_color_dark' ) ) ) {
			$byline_conditional['conditional'][] = [
				'textColor'  => '#byline_color_dark#',
				'conditions' => [
					'minSpecVersion'       => '1.14',
					'preferredColorScheme' => 'dark',
				],
			];
		}

		// Separate handling for byline link styles.
		if ( 'yes' === $theme->get_value( 'author_links' ) ) {
			if ( ! empty( $theme->get_value( 'author_link_color' ) ) ) {
				$byline_conditional['linkStyle'] = [
					'textColor' => '#author_link_color#',
				];
			}

			if ( ! empty( $theme->get_value( 'author_link_color_dark' ) ) ) {
				$byline_conditional['conditional'][] = [
					'linkStyle'  => [
						'textColor' => '#author_link_color_dark#',
					],
					'conditions' => [
						'minSpecVersion'       => '1.14',
						'preferredColorScheme' => 'dark',
					],
				];
			}
		}

		$this->register_spec(
			'default-byline',
			__( 'Style', 'apple-news' ),
			array_merge(
				[
					'textAlignment' => '#text_alignment#',
					'fontName'      => '#byline_font#',
					'fontSize'      => '#byline_size#',
					'lineHeight'    => '#byline_line_height#',
					'tracking'      => '#byline_tracking#',
					'textColor'     => '#byline_color#',
				],
				$byline_conditional
			)
		);

		$this->register_spec(
			'byline-layout',
			__( 'Layout', 'apple-news' ),
			[
				'margin' => [
					'top'    => 10,
					'bottom' => 10,
				],
			]
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 * @access protected
	 */
	protected function build( $html ) {

		// If there is no text for this element, bail.
		$check = trim( $html );
		if ( empty( $check ) ) {
			return;
		}

		$this->register_json(
			'json',
			[
				'#text#' => $html,
			]
		);

		$this->set_default_style();
		$this->set_default_layout();
	}

	/**
	 * Set the default style for the component.
	 *
	 * @access private
	 */
	private function set_default_style() {

		// Get information about the currently loaded theme.
		$theme = \Apple_Exporter\Theme::get_used();

		$byline_conditional = [];

		if ( ! empty( $theme->get_value( 'byline_color_dark' ) ) ) {
			$byline_conditional[] = [
				'#byline_color_dark#' => $theme->get_value( 'byline_color_dark' ),
			];
		}

		// Separate handling for byline link styles.
		if ( 'yes' === $theme->get_value( 'author_links' ) ) {
			if ( ! empty( $theme->get_value( 'author_link_color' ) ) ) {
				$byline_conditional[] = [
					'#author_link_color#' => $theme->get_value( 'author_link_color' ),
				];
			}

			if ( ! empty( $theme->get_value( 'author_link_color_dark' ) ) ) {
				$byline_conditional[] = [
					'#author_link_color_dark#' => $theme->get_value( 'author_link_color_dark' ),
				];
			}
		}

		$this->register_style(
			'default-byline',
			'default-byline',
			array_merge(
				[
					'#text_alignment#'     => $this->find_text_alignment(),
					'#byline_font#'        => $theme->get_value( 'byline_font' ),
					'#byline_size#'        => intval( $theme->get_value( 'byline_size' ) ),
					'#byline_line_height#' => intval( $theme->get_value( 'byline_line_height' ) ),
					'#byline_tracking#'    => intval( $theme->get_value( 'byline_tracking' ) ) / 100,
					'#byline_color#'       => $theme->get_value( 'byline_color' ),
				],
				$byline_conditional
			),
			'textStyle'
		);
	}

	/**
	 * Set the default layout for the component.
	 *
	 * @access private
	 */
	private function set_default_layout() {
		$this->register_full_width_layout(
			'byline-layout',
			'byline-layout',
			[],
			'layout'
		);
	}
}
