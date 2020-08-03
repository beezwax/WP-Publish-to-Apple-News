<?php
/**
 * Publish to Apple News: \Apple_Exporter\Components\End_Of_Article class
 *
 * @package Apple_News
 * @subpackage Apple_Exporter\Components
 */

namespace Apple_Exporter\Components;

/**
 * Represents an Article Format End_Of_Article. It gets generated automatically
 * so not much to do here but define the static JSON.
 *
 * @since 0.4.0
 */
class End_Of_Article extends Component {

	/**
	 * Register all specs for the component.
	 *
	 * @access public
	 */
	public function register_specs() {
		$this->register_spec(
			'json',
			__( 'JSON', 'apple-news' ),
			array()
		);

		$this->register_spec(
			'layout',
			__( 'Layout', 'apple-news' ),
			array()
		);
	}

	/**
	 * Build the component.
	 *
	 * @param string $html The HTML to parse into text for processing.
	 * @access protected
	 */
	protected function build( $html ) {
		$this->register_json(
			'json',
			array()
		);

		$this->set_layout();
	}

	/**
	 * Set the layout for the component.
	 *
	 * @access private
	 */
	private function set_layout() {
		$this->register_full_width_layout(
			'end-of-article-layout',
			'layout',
			array(),
			'layout'
		);
	}

}
