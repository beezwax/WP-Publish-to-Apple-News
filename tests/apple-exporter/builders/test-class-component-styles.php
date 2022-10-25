<?php
/**
 * Publish to Apple News Tests: Apple_News_Component_Styles_Tests class
 *
 * Contains a class which is used to test \Apple_Exporter\Builders\Component_Styles.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Builders\Component_Styles;

/**
 * A class which is used to test \Apple_Exporter\Builders\Component_Styles.
 */
class Apple_News_Component_Styles_Tests extends Apple_News_Testcase {

	/**
	 * Tests the functionality of the builder.
	 *
	 * @see \Apple_Exporter\Builders\Component_Styles::build()
	 *
	 * @access public
	 */
	public function testBuiltArray() {
		$styles = new Component_Styles( $this->content, $this->settings );
		$styles->register_style( 'some-name', array( 'my-key' => 'my value' ) );
		$result = $styles->to_array();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( array( 'my-key' => 'my value' ), $result['some-name'] );
	}
}
