<?php
/**
 * Publish to Apple News Tests: Apple_News_Component_Text_Styles_Test class
 *
 * Contains a class to test the functionality of the Apple_Exporter\Builders\Component_Text_Styles class.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Builders\Component_Text_Styles;

/**
 * A class to test the behavior of the Apple_Exporter\Builders\Component_Text_Styles class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Component_Text_Styles_Test extends Apple_News_Testcase {

	/**
	 * Tests the behavior of the componentTextStyles builder.
	 */
	public function test_built_array() {
		$styles = new Component_Text_Styles( $this->content, $this->settings );
		$styles->register_style( 'some-name', 'my value' );
		$result = $styles->to_array();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 'my value', $result['some-name'] );
	}
}
