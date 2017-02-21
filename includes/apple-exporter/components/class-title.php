<?php
namespace Apple_Exporter\Components;

class Title extends Component {

	/**
	 * Register all specs for the component.
	 *
	 * @param string $text
	 * @access protected
	 */
	public function register_specs() {

	}

	/**
	 * Build the component.
	 *
	 * @param string $text
	 * @access protected
	 */
	protected function build( $text ) {
		$this->json = array(
			'role' => 'title',
			'text' => $text,
		);

		$this->register_json(
			array(
				'role' => 'title',
				'text' => '%%text%%',
			),
			array(
				'text' => $text,
			)
	 );

		$this->set_style();
		$this->set_layout();
	}

	/**
	 * Set the style for the component.
	 *
	 * @access private
	 */
	private function set_style() {
		$this->register_style(
			'default-title',
			array(
				'fontName' => '%%header1_font%%',
				'fontSize' => '%%header1_size%%',
				'lineHeight' => '%%header1_line_height%%',
				'tracking' => '%%header1_tracking%%',
				'textColor' => '%%header1_color%%',
				'textAlignment' => '%%text_alignment%%',
			),
			array(
				'fontName' => $this->get_setting( 'header1_font' ),
				'fontSize' => intval( $this->get_setting( 'header1_size' ) ),
				'lineHeight' => intval( $this->get_setting( 'header1_line_height' ) ),
				'tracking' => intval( $this->get_setting( 'header1_tracking' ) ) / 100,
				'textColor' => $this->get_setting( 'header1_color' ),
				'textAlignment' => $this->find_text_alignment(),
			),
			'textStyle'
		 );
	}

	/**
	 * Set the layout for the component.
	 *
	 * @access private
	 */
	private function set_layout() {
		$this->register_layout(
			'title-layout',
			array(
				'margin' => array(
					'top' => 30,
					'bottom' => 0,
				),
			),
			array(),
			'layout'
		);
	}

}

