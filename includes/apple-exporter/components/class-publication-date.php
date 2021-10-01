<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\Publication_Date class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * A publication_date normally describes who wrote the article, the date, etc.
 *
 * @since 0.2.0
 */
class Publication_Date extends Component {

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
			array(
				'role' => 'publication-date',
				'text' => '#text#',
			)
		);

		$this->register_spec(
			'default-publication-date',
			__( 'Style', 'apple-news' ),
			(
				array(
					'textAlignment' => '#text_alignment#',
					'fontName'      => '#publication_date_font#',
					'fontSize'      => '#publication_date_size#',
					'lineHeight'    => '#publication_date_line_height#',
					'tracking'      => '#publication_date_tracking#',
					'textColor'     => '#publication_date_color#',
				) + (
					! empty( $theme->get_value( 'publication_date_color_dark' ) )
						? array(
							'conditional' => array(
								'textColor'  => '#publication_date_color_dark#',
								'conditions' => array(
									'minSpecVersion'       => '1.14',
									'preferredColorScheme' => 'dark',
								),
							),
						)
						: array()
				)
			)
		);

		$this->register_spec(
			'publication-date-layout',
			__( 'Layout', 'apple-news' ),
			array(
				'margin' => array(
					'top'    => 10,
					'bottom' => 10,
				),
			)
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
			array(
				'#text#' => $html,
			)
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

		$this->register_style(
			'default-publication-date',
			'default-publication-date',
			(
				array(
					'#text_alignment#'     => $this->find_text_alignment(),
					'#publication_date_font#'        => $theme->get_value( 'publication_date_font' ),
					'#publication_date_size#'        => intval( $theme->get_value( 'publication_date_size' ) ),
					'#publication_date_line_height#' => intval( $theme->get_value( 'publication_date_line_height' ) ),
					'#publication_date_tracking#'    => intval( $theme->get_value( 'publication_date_tracking' ) ) / 100,
					'#publication_date_color#'       => $theme->get_value( 'publication_date_color' ),
				) + (
					! empty( $theme->get_value( 'publication_date_color_dark' ) )
						? array( '#publication_date_color_dark' => $theme->get_value( 'publication_date_color_dark' ) )
						: array()
				)
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
			'publication-date-layout',
			'publication-date-layout',
			array(),
			'layout'
		);
	}

}

