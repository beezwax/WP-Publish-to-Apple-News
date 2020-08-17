<?php
/**
 * Publish to Apple News tests: Test_End_Of_Article class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\End_Of_Article class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Test_End_Of_Article extends Component_TestCase {

	/**
	 * Test default End Of Article behavior
	 */
	public function testDefaultEndOfArticleSetting() {
		$post_id = self::factory()->post->create();
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 4, count( $json['components'] ) );
	}

	/**
	 * Test adding of End Of Article JSON content
	 */
	public function testEndOfArticleContent() {
		// Setup.
		$this->set_theme_settings(
			[
				'json_templates' => [
					'end_of_article' => [
						'json'   => [
							'role' => 'heading',
						],
						'layout' => [],
					],
				],
			]
		);

		$post_id = self::factory()->post->create();
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 5, count( $json['components'] ) );
		$this->assertEquals( 'heading', $json['components'][4]['role'] );
	}
}
