<?php
namespace Apple_Exporter\Components;

/**
 * Some Exporter_Content object might have an intro parameter.
 * This component does not need a node so no need to implement match_node.
 *
 * @since 0.2.0
 */
class Intro extends Component {

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
		$this->register_json(
			array(
				'role' => 'intro',
				'text' => '%%text%%',
			),
			array(
				'text' => $text . "\n",
			)
	 );

		$this->set_style();
	}

	/**
	 * Set the style for the component.
	 *
	 * @access private
	 */
	private function set_style() {
		$this->json[ 'textStyle' ] = 'default-intro';
		$this->register_style(
			'default-intro',
			array(
				'fontName'   => '%%body_font%%',
				'fontSize'   => '%%body_size%%',
				'lineHeight' => '%%body_line_height%%',
				'textColor'  => '%%body_color%%',
			),
			array(
				'fontName'   => $this->get_setting( 'body_font' ),
				'fontSize'   => intval( $this->get_setting( 'body_size' ) ),
				'lineHeight' => intval( $this->get_setting( 'body_line_height' ) ),
				'textColor'  => $this->get_setting( 'body_color' ),
			),
			'textStyle'
		);
	}

}

