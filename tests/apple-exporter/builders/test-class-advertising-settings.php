<?php
/**
 * Publish to Apple News tests: Test_Class_Advertising_Settings class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Builders\Advertising_Settings;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Builders\Advertising_Settings class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Test_Class_Advertising_Settings extends Apple_News_Testcase {

	/**
	 * Tests the default advertising settings.
	 */
	public function testDefaultAdSettings() {
		$builder = new Advertising_Settings( $this->content, $this->content_settings );
		$result = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 5, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 15, $result['layout']['margin']['top'] );
		$this->assertEquals( 15, $result['layout']['margin']['bottom'] );
	}

	/**
	 * Tests the behavior of the component when advertisements are disabled.
	 */
	public function testNoAds() {

		// Setup.
		$settings = $this->theme->all_settings();
		$settings['enable_advertisement'] = 'no';
		$this->theme->load( $settings );
		$this->assertTrue( $this->theme->save() );

		// Test.
		$builder = new Advertising_Settings( $this->content, $this->content_settings );
		$result = $builder->to_array();
		$this->assertEquals( 0, count( $result ) );
	}

	/**
	 * Tests the ability to customize ad frequency.
	 */
	public function testCustomAdFrequency() {

		// Setup.
		$settings = $this->theme->all_settings();
		$settings['ad_frequency'] = 10;
		$this->theme->load( $settings );
		$this->assertTrue( $this->theme->save() );

		// Test.
		$builder = new Advertising_Settings( $this->content, $this->content_settings );
		$result  = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 10, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 15, $result['layout']['margin']['top'] );
		$this->assertEquals( 15, $result['layout']['margin']['bottom'] );
	}

	/**
	 * Tests the ability to customize the ad margin.
	 */
	public function testCustomAdMargin() {

		// Setup.
		$settings = $this->theme->all_settings();
		$settings['ad_margin'] = 20;
		$this->theme->load( $settings );
		$this->assertTrue( $this->theme->save() );

		// Test.
		$builder = new Advertising_Settings( $this->content, $this->content_settings );
		$result  = $builder->to_array();
		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 5, $result['frequency'] );
		$this->assertEquals( 1, count( $result['layout'] ) );
		$this->assertEquals( 20, $result['layout']['margin']['top'] );
		$this->assertEquals( 20, $result['layout']['margin']['bottom'] );
	}
}
