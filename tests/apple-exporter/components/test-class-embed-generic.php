<?php
/**
 * Publish to Apple News Tests: Embed_Generic class
 *
 * Contains a class which is used to test Apple_Exporter\Components\Embed_Generic.
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Embed_Generic;

/**
 * A class which is used to test the Apple_Exporter\Components\Embed_Generic class.
 */
class Embed_Generic_Test extends Component_TestCase {

	/**
	 * A data provider for the test_transform function.
	 *
	 * @see self::test_transform()
	 *
	 * @access public
	 * @return array An array of test data.
	 */
	public function data_transform() {
		return [
			// Gutenberg: Amazon Kindle embed.
			[
				<<<HTML
<figure class="wp-block-embed-amazon-kindle wp-block-embed is-type-rich is-provider-amazon"><div class="wp-block-embed__wrapper">
<iframe title="The Design of Everyday Things: Revised and Expanded Edition" type="text/html" width="640" height="550" frameborder="0" allowfullscreen style="max-width:100%" src="https://read.amazon.com/kp/card?preview=inline&#038;linkCode=kpd&#038;ref_=k4w_oembed_7cXLROJYP0bDqM&#038;asin=B00E257T6C&#038;tag=kpembed-20"></iframe>
</div></figure>
HTML
			],

			// Gutenberg: Animoto embed.
			[
				<<<HTML
<figure class="wp-block-embed-animoto wp-block-embed is-type-video is-provider-animoto wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
<iframe title="Video Player" id="vp1WmGs0" width="640" height="360" frameborder="0" src="https://s3.amazonaws.com/embed.animoto.com/play.html?w=swf/production/vp1&#038;e=1565635838&#038;f=WmGs0SgMeHvBMur0fL68rw&#038;d=0&#038;m=b&#038;r=360p+480p+720p&#038;i=m&#038;asset_domain=s3-p.animoto.com&#038;animoto_domain=animoto.com&#038;options=" allowfullscreen></iframe>
</div></figure>
HTML
			],

			// Gutenberg: Cloudup embed.
			[
				<<<HTML
<figure class="wp-block-embed-cloudup wp-block-embed is-type-rich is-provider-cloudup wp-embed-aspect-4-3 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
<iframe title="Video Stream - share clips and home movies" src="https://cloudup.com/cjZ6QGIsErH?chromeless" data-uid="cjZ6QGIsErH" data-aspect-ratio='1.3704496788008564' width="640" height="467" scrolling="no" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true" allowfullscreen="true" class="cloudup_iframe_embed"></iframe>
</div></figure>
HTML
			],
		];
	}

	/**
	 * A filter function to modify the URL in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @access public
	 * @return array The modified JSON.
	 *
	public function filter_apple_news_embed_generic_json( $json ) {
		$json['URL'] = 'https://www.embed_generic.com/test/posts/54321';

		return $json;
	}

	/**
	 * Test the `apple_news_embed_generic_json` filter.
	 *
	 * @access public
	 *
	public function testFilter() {

		// Setup.
		$component = new Embed_Generic(
			'https://www.embed_generic.com/test/posts/12345',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_embed_generic_json',
			array( $this, 'filter_apple_news_embed_generic_json' )
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals(
			'https://www.embed_generic.com/test/posts/54321',
			$result['URL']
		);

		// Teardown.
		remove_filter(
			'apple_news_embed_generic_json',
			array( $this, 'filter_apple_news_embed_generic_json' )
		);
	}

	/**
	 * Tests the transformation process from oEmbed HTML to an Embed Generic component.
	 *
	 * @dataProvider data_transform
	 *
	 * @param string $html The HTML to test.
	 *
	 * @access public
	 */
	public function testTransform( $html, $title,  ) {

		// Setup.
		$component = new Embed_Generic(
			$html,
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			[
				'layout'     => 'embed-generic-layout',
				'role'       => 'container',
				'components' => [
					[
						'role'   => 'heading2',
						'text'   => 'TITLE GOES HERE',
						'format' => 'html',
					],
					[
						'role'      => 'body',
						'text'      => '<a href="' . esc_url( 'https://example.com' ) . '">' . esc_html__( 'View on PROVIDER.', 'apple-news' ) . '</a>',
						'format'    => 'html',
						'textStyle' => [
							'fontSize' => 14,
						],
					],
				],
			],
			$component->to_array()
		);

		// Ensure that the node match returns true for valid signatures.
		$node = self::build_node( $html );
		$this->assertEquals(
			$component->node_matches( $node ),
			$node
		);
	}
}
