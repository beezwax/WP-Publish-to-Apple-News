<?php
/**
 * Publish to Apple News tests: Intro_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Intro;
use Apple_Actions\Index\Export;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Intro class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Intro_Test extends Component_TestCase {

	/**
	 * Tests the build of an Intro component.
	 */
	public function testBuild() {
		$component = new Intro(
			'Test intro text.',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role' => 'intro',
				'text' => "Test intro text.\n",
				'textStyle' => 'default-intro',
		 	),
			$component->to_array()
		);
	}

	/**
	 * Tests the filter for intro content.
	 */
	public function testFilter() {
		$component = new Intro(
			'Test intro text.',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		add_filter(
			'apple_news_intro_json',
			function( $json ) {
				$json['textStyle'] = 'fancy-intro';
				return $json;
			}
		);

		$this->assertEquals(
			array(
				'role' => 'intro',
				'text' => "Test intro text.\n",
				'textStyle' => 'fancy-intro',
		 	),
			$component->to_array()
		);
	}

	/**
	 * Ensures that the Intro component is skipped if there is no intro
	 * specified. Intros can be specified either via customizing the
	 * excerpt for a post.
	 */
	public function testSkip() {
		// Set up the theme to have a specific component order that includes the intro.
		$this->set_theme_settings( [ 'meta_component_order' => [ 'cover', 'title', 'byline', 'intro' ] ] );

		// Create an example post without a customized excerpt.
		$sample_post = self::factory()->post->create(
			[
				'post_content' => '<p>Lorem ipsum dolor sit amet.</p>',
				'post_excerpt' => '',
			]
		);

		// Run the exporter against the sample post and verify that the Intro component is not used, since there is no custom excerpt.
		$export           = new Export( $this->settings, $sample_post );
		$exporter         = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();
		$this->assertEquals( '', $exporter_content->intro() );

		// Create an example post with a customized excerpt.
		$sample_post = self::factory()->post->create(
			[
				'post_content' => '<p>Lorem ipsum dolor sit amet.</p>',
				'post_excerpt' => 'Sample excerpt',
			]
		);

		// Run the exporter against the sample post and verify that the Intro component is used, and matches the custom excerpt.
		$export           = new Export( $this->settings, $sample_post );
		$exporter         = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();
		$this->assertEquals( 'Sample excerpt', $exporter_content->intro() );

		// Create an example post with a bit more content and a custom excerpt that matches the first part of the content.
		$sample_post = self::factory()->post->create(
			[
				'post_content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis arcu risus, vestibulum non nulla a, mollis posuere lectus. Quisque lectus ex, viverra nec massa et, elementum sodales dui. Nam nec congue libero. Nunc eu lectus quis quam eleifend gravida. Nulla condimentum, nisl ornare rhoncus ultrices, ex ipsum luctus dolor, vitae iaculis metus magna vitae neque. Maecenas in risus id est hendrerit mattis. Curabitur pulvinar ante a ligula tincidunt, id porta ante ornare. Donec neque metus, hendrerit nec lectus in, consectetur porta dolor. Curabitur egestas orci eu tortor congue, eu varius ipsum finibus. In in faucibus mi. Donec odio leo, blandit non varius nec, cursus ac eros. Aenean sagittis mauris eget interdum elementum. Etiam hendrerit lectus at lacus pretium pretium. Vivamus eu egestas dolor. Nam a ultricies lectus.</p>',
				'post_excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis arcu risus, vestibulum non nulla a, mollis posuere lectus.',
			]
		);

		// Run the exporter against the sample post and verify that the Intro component is not used because it duplicates content from the main body.
		$export           = new Export( $this->settings, $sample_post );
		$exporter         = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();
		$this->assertEquals( '', $exporter_content->intro() );
	}
}
