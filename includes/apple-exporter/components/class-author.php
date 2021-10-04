<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Author class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * A author normally describes who wrote the article, the date, etc.
 *
 * @since 0.2.0
 */
class Author extends Component {

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$theme = \Apple_Exporter\Theme::get_used();

		$this->register_spec(
			'author-json',
			__( 'Author JSON', 'apple-news' ),
			[
				'role'   => 'author',
				'text'   => '#text#',
				'format' => 'html',
			]
		);

		// Author style conditional.
		$author_conditional = [];

		if ( ! empty( $theme->get_value( 'author_color_dark' ) ) ) {
			$author_conditional['conditional'][] = [
				'textColor'  => '#author_color_dark#',
				'conditions' => [
					'minSpecVersion'       => '1.14',
					'preferredColorScheme' => 'dark',
				],
			];
		}

		// Separate handling for author link styles.
		if ( 'yes' === $theme->get_value( 'author_links' ) ) {
			if ( ! empty( $theme->get_value( 'author_link_color' ) ) ) {
				$author_conditional['linkStyle'] = [
					'textColor' => '#author_link_color#',
				];
			}

			if ( ! empty( $theme->get_value( 'author_link_color_dark' ) ) ) {
				$author_conditional['conditional'][] = [
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
			'default-author',
			__( 'Style', 'apple-news' ),
			array_merge(
				[
					'textAlignment' => '#text_alignment#',
					'fontName'      => '#author_font#',
					'fontSize'      => '#author_size#',
					'lineHeight'    => '#author_line_height#',
					'tracking'      => '#author_tracking#',
					'textColor'     => '#author_color#',
				],
				$author_conditional
			)
		);

		$this->register_spec(
			'author-layout',
			__( 'Layout', 'apple-news' ),
			[
				'margin' => [
					'top' => 10,
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
			'author-json',
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

		$author_conditional = [];

		if ( ! empty( $theme->get_value( 'author_color_dark' ) ) ) {
			$author_conditional[] = [
				'#author_color_dark#' => $theme->get_value( 'author_color_dark' ),
			];
		}

		// Separate handling for author link styles.
		if ( 'yes' === $theme->get_value( 'author_links' ) ) {
			if ( ! empty( $theme->get_value( 'author_link_color' ) ) ) {
				$author_conditional[] = [
					'#author_link_color#' => $theme->get_value( 'author_link_color' ),
				];
			}

			if ( ! empty( $theme->get_value( 'author_link_color_dark' ) ) ) {
				$author_conditional[] = [
					'#author_link_color_dark#' => $theme->get_value( 'author_link_color_dark' ),
				];
			}
		}

		$this->register_style(
			'default-author',
			'default-author',
			array_merge(
				[
					'#text_alignment#'     => $this->find_text_alignment(),
					'#author_font#'        => $theme->get_value( 'author_font' ),
					'#author_size#'        => intval( $theme->get_value( 'author_size' ) ),
					'#author_line_height#' => intval( $theme->get_value( 'author_line_height' ) ),
					'#author_tracking#'    => intval( $theme->get_value( 'author_tracking' ) ) / 100,
					'#author_color#'       => $theme->get_value( 'author_color' ),
				],
				$author_conditional
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
			'author-layout',
			'author-layout',
			[],
			'layout'
		);
	}
}
