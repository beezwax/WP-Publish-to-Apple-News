<?php
/**
 * Publish to Apple News Tests: Flickr_Test class
 *
 * Contains a class which is used to test Apple_Exporter\Components\Flickr.
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Flickr;

/**
 * A class which is used to test the Apple_Exporter\Components\Flickr class.
 */
class Flickr_Test extends Component_TestCase {

	/**
	 * A data provider for the test_transform function.
	 *
	 * @see self::test_transform()
	 *
	 * @access public
	 * @return array An array of test data
	 */
	public function data_transform() {
		return array(
			array( 'https://www.flickr.com/photos/151766161@N07/48306461847/' ),
		);
	}

	/**
	 * A filter function to modify the URL in the generated JSON.
	 *
	 * @param array $json The JSON array to modify.
	 *
	 * @access public
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_flickr_json( $json ) {
		$json['URL'] = 'https://www.flickr.com/photos/12345/54321/';

		return $json;
	}

		/**
	 * Test the `apple_news_flickr_json` filter.
	 *
	 * @access public
	 */
	public function testFilter() {

		// Setup.
		$component = new Flickr(
			'https://www.flickr.com/photos/99999/99999/',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);
		add_filter(
			'apple_news_flickr_json',
			array( $this, 'filter_apple_news_flickr_json' )
		);

		// Test.
		$result = $component->to_array();
		$this->assertEquals(
			'https://www.flickr.com/photos/12345/54321/',
			$result['URL']
		);

		// Teardown.
		remove_filter(
			'apple_news_flickr_json',
			array( $this, 'filter_apple_news_flickr_json' )
		);
	}

	/**
	 * Tests the transformation process from an oEmbed URL to a Flickr component.
	 *
	 * @dataProvider data_transform
	 *
	 * @param string $url The URL to test.
	 *
	 * @access public
	 */
	public function testTransform( $url ) {
		// Setup. Single photo, no article caption, full-view caption
		$component = new Flickr(
			'<figure class="wp-block-embed-flickr wp-block-embed is-type-photo is-provider-flickr"><div class="wp-block-embed__wrapper">
<a href="https://www.flickr.com/photos/hajdekr/48293159411/in/pool-503531@N24"><img src="https://live.staticflickr.com/65535/48293159411_f297105a3c_z.jpg" alt="&#x160;koda 1100 OHC (MOC - 4K)" width="640" height="360"/></a>
</div></figure>',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test Photo
		$this->assertEquals(
			array(
				'role' => 'photo',
				'URL' => 'https://live.staticflickr.com/65535/48293159411_f297105a3c_z.jpg',
				'caption' => array(
					'text' => '&#x160;koda 1100 OHC (MOC - 4K)',
					'format' => 'html'
				)
			),
			$component->to_array()['components'][0] // Photo component
		);

		// Test for hidden caption
		$this->assertEquals(
			true,
			$component->to_array()['components'][1]['hidden']
		);

		// Setup. Single photo, article caption, full-view caption
		$component = new Flickr(
			'<figure class="wp-block-embed-flickr wp-block-embed is-type-photo is-provider-flickr"><div class="wp-block-embed__wrapper">
<a href="https://www.flickr.com/photos/hajdekr/48293159411/in/pool-503531@N24"><img src="https://live.staticflickr.com/65535/48293159411_f297105a3c_z.jpg" alt="&#x160;koda 1100 OHC (MOC - 4K)" width="640" height="360"/></a>
</div><figcaption><strong>Bold</strong> <em>Italic</em> <a href="http://Link.com">Link</a></figcaption></figure>',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test for Caption
		$this->assertEquals(
			array(
				'role' => 'caption',
				'text' => '<strong>Bold</strong> <em>Italic</em> <a href="http://Link.com">Link</a>',
				'format' => 'html',
				'hidden' => false
			),
			$component->to_array()['components'][1]
		);

		// Setup. Album, 'view on flickr' article caption
		$component = new Flickr(
			'<figure class="wp-block-embed-flickr wp-block-embed is-type-rich is-provider-flickr"><div class="wp-block-embed__wrapper">
e<a data-flickr-embed="true" href="https://www.flickr.com/photos/ianhoy/albums/72157703843453165" title="Not just a pet - Hedwig by DOGOD Brick Design, on Flickr"><img src="https://live.staticflickr.com/4894/31041335057_cdbaed87a8_z.jpg" width="640" height="480" alt="DOGOD_Hedwig_S02"/></a><script async="" src="https://embedr.flickr.com/assets/client-code.js" charset="utf-8"/></div></figure>',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test Photo
		$this->assertEquals(
			array(
				'role' => 'photo',
				'URL' => 'https://live.staticflickr.com/4894/31041335057_cdbaed87a8_z.jpg',
				'caption' => array(
					'text' => '<a href="https://www.flickr.com/photos/ianhoy/albums/72157703843453165">Not just a pet - Hedwig by DOGOD Brick Design, on Flickr</a>',
					'format' => 'html'
				)
			),
			$component->to_array()['components'][0] // Photo component
		);

		// Test for Caption
		$this->assertEquals(
			array(
				'role' => 'caption',
				'text' => '<a href="https://www.flickr.com/photos/ianhoy/albums/72157703843453165">Not just a pet - Hedwig by DOGOD Brick Design, on Flickr</a>',
				'format' => 'html',
				'hidden' => false
			),
			$component->to_array()['components'][1]
		);

		// Setup. Album, 'view on flickr' article caption
		$component = new Flickr(
			'<figure class="wp-block-embed-flickr wp-block-embed is-type-rich is-provider-flickr"><div class="wp-block-embed__wrapper">
e<a data-flickr-embed="true" href="https://www.flickr.com/photos/ianhoy/albums/72157703843453165" title="Not just a pet - Hedwig by DOGOD Brick Design, on Flickr"><img src="https://live.staticflickr.com/4894/31041335057_cdbaed87a8_z.jpg" width="640" height="480" alt="DOGOD_Hedwig_S02"/></a><script async="" src="https://embedr.flickr.com/assets/client-code.js" charset="utf-8"/></div><figcaption><strong>Bold Caption</strong></figcaption></figure>',
			null,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test for Caption
		$this->assertEquals(
			array(
				'role' => 'caption',
				'text' => '<strong>Bold Caption</strong><br><a href="https://www.flickr.com/photos/ianhoy/albums/72157703843453165">Not just a pet - Hedwig by DOGOD Brick Design, on Flickr</a>',
				'format' => 'html',
				'hidden' => false
			),
			$component->to_array()['components'][1]
		);
	}
}
