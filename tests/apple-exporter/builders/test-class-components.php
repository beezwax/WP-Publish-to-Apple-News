<?php
/**
 * Publish to Apple News Tests: Component_Tests class
 *
 * Contains a class which is used to test \Apple_Exporter\Builders\Components.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use \Apple_Exporter\Component_Factory;
use \Apple_Exporter\Exporter_Content;
use \Apple_Exporter\Settings;
use \Apple_Exporter\Workspace;
use \Apple_Exporter\Builders\Components;
use \Apple_Exporter\Builders\Component_Layouts;
use \Apple_Exporter\Builders\Component_Text_Styles;

/**
 * A class which is used to test the \Apple_Exporter\Builders\Components class.
 */
class Component_Tests extends Apple_News_Testcase {

	/**
	 * A data provider for the full size image URL test.
	 *
	 * @return array
	 */
	public function dataImageFullSizeUrl() {
		return [
			// An image without crops should return itself.
			[
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
			],

			// An image with a crop should return the original image without the crop.
			[
				'http://example.org/wp-content/uploads/2020/07/image-150x150.jpg',
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
			],

			// Scaled images should return the un-scaled version.
			[
				'http://example.org/wp-content/uploads/2020/07/image-scaled.jpg',
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
			],

			// Rotated images should return the un-rotated version.
			[
				'http://example.org/wp-content/uploads/2020/07/image-rotated.jpg',
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
			],

			// Photon images should return the original.
			[
				'http://example.org/wp-content/uploads/2020/07/image.jpg?w=234&crop=0%2C5px%2C100%2C134px&ssl=1',
				'http://example.org/wp-content/uploads/2020/07/image.jpg',
			],
		];
	}

	/**
	 * A data provider for the meta component ordering test.
	 *
	 * @see self::testMetaComponentOrdering()
	 *
	 * @access public
	 * @return array An array of arguments to pass to the test function.
	 */
	public function dataMetaComponentOrdering() {
		return array(
			array(
				array( 'cover', 'title', 'byline' ),
				array( 'header', 'container' ),
				array( 'title', 'byline' ),
			),
			array(
				array( 'byline', 'cover', 'title' ),
				array( 'byline', 'header', 'container' ),
				array( 'title' ),
			),
			array(
				array( 'title', 'byline' ),
				array( 'title', 'byline' ),
				array(),
			),
			array(
				array( 'cover', 'byline' ),
				array( 'header', 'container' ),
				array( 'byline' ),
			),
			array(
				array( 'cover', 'title' ),
				array( 'header', 'container' ),
				array( 'title' ),
			),
		);
	}

	/**
	 * Actions to be run before each test function.
	 *
	 * @access public
	 */
	public function setup() {
		parent::setUp();

		// Setup.
		$themes = new Admin_Apple_Themes;
		$themes->setup_theme_pages();
		$file1 = dirname( dirname( __DIR__ ) ) . '/data/test-image.jpg';
		$file2 = dirname( dirname( __DIR__ ) ) . '/data/test-image.jpg';
		$this->cover = $this->factory->attachment->create_upload_object( $file1 );
		$this->image2 = $this->factory->attachment->create_upload_object( $file2 );
		$this->settings = new Settings;
		$this->content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			'',
			null,
			wp_get_attachment_url( $this->cover ),
			'Author Name'
		);
		$this->styles = new Component_Text_Styles( $this->content, $this->settings );
		$this->layouts = new Component_Layouts( $this->content, $this->settings );
		Component_Factory::initialize(
			new Workspace( 1 ),
			$this->settings,
			$this->styles,
			$this->layouts
		);
	}

	/**
	 * Actions to be run after every test.
	 *
	 * @access public
	 */
	public function tearDown() {
		$theme = new \Apple_Exporter\Theme;
		$theme->set_name( \Apple_Exporter\Theme::get_active_theme_name() );
		$theme->delete();
	}

	/**
	 * Tests the ability to provide cover configuration as an array instead of
	 * a URL, which lets us build cover images with captions.
	 */
	public function testCoverImageArrayConfig() {
		$this->set_theme_settings( [ 'cover_caption' => true ] );
		$cover_url = wp_get_attachment_url( $this->cover );
		$content   = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			null,
			[
				'caption' => 'Test Caption',
				'url'     => $cover_url,
			],
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result  = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( $cover_url, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'Test Caption', $result[0]['components'][0]['caption']['text'] );
		$this->assertEquals( 'caption', $result[0]['components'][1]['role'] );
		$this->assertEquals( 'Test Caption', $result[0]['components'][1]['text'] );

		// Test setting the caption from within the content rather than as part of the Exporter_Content config (featured image).
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p><figure class="wp-block-image size-full"><img src="' . $cover_url . '" alt="" class="wp-image-' . $this->cover . '"/><figcaption>Test caption!</figcaption></figure>',
			null,
			null,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayoutWithCaption', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $cover_url, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'Test caption!', $result[0]['components'][0]['caption']['text'] );
	}

	/**
	 * Tests the image deduping functionality of the Components class.
	 *
	 * Ensures that a featured image with the same source URL (minus any crops)
	 * as the first image in the post does not result in the same image
	 * appearing twice in a row. This is accomplished by ignoring the featured
	 * image and instead extracting the first image from the post to use as the
	 * cover image.
	 */
	public function testFeaturedImageDeduping() {
		$this->set_theme_settings( [ 'cover_caption' => true ] );

		// Get image URLs for the two attachment images created during setUp.
		$image1           = wp_get_attachment_url( $this->cover );
		$image2           = wp_get_attachment_url( $this->image2 );
		$image1_thumbnail = wp_get_attachment_image_url( $this->cover );
		$image2_thumbnail = wp_get_attachment_image_url( $this->image2 );

		/*
		 * Scenario 1:
		 * - No featured image is set.
		 * - No images in the content.
		 * Expected: No cover image is set.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			null,
			null,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertNotEquals( 'headerPhotoLayout', $result[0]['layout'] );

		/*
		 * Scenario 2:
		 * - A featured image is set.
		 * - No images in the content.
		 * Expected: The featured image is set as the cover image.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>',
			null,
			$image1,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1, $result[0]['components'][0]['URL'] );

		/*
		 * Scenario 3:
		 * - A featured image is set.
		 * - Images in the content, but not the same ones as the featured image.
		 * Expected: The featured image is set as the cover image and the body image is still in the body.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>' . wp_get_attachment_image( $this->image2 ),
			null,
			$image1,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][3]['role'] );
		$this->assertEquals( $image2_thumbnail, $result[1]['components'][3]['URL'] );

		/*
		 * Scenario 4:
		 * - A featured image is set.
		 * - Images in the content, including the same one as the featured image, but the featured image is not first.
		 * Expected: The featured image is set as the cover image and the body image is still in the body.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>' . wp_get_attachment_image( $this->image2 ) . wp_get_attachment_image( $this->cover ),
			null,
			$image1,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][3]['role'] );
		$this->assertEquals( $image2_thumbnail, $result[1]['components'][3]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][4]['role'] );
		$this->assertEquals( $image1_thumbnail, $result[1]['components'][4]['URL'] );

		/*
		 * Scenario 5:
		 * - A featured image is set.
		 * - Images in the content, including the same one as the featured image, and the featured image is first.
		 * Expected: The first image from the content is set as the cover image and the first image from the content has been removed. The featured image is ignored.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>' . wp_get_attachment_image( $this->cover ) . wp_get_attachment_image( $this->image2 ),
			null,
			$image1,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1_thumbnail, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][3]['role'] );
		$this->assertEquals( $image2_thumbnail, $result[1]['components'][3]['URL'] );
		$this->assertEquals( 4, count( $result[1]['components'] ) );

		/*
		 * Scenario 6:
		 * - No featured image is set.
		 * - Images in the content.
		 * Expected: The first image from the content is set as the cover image and the first image from the content has been removed.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>' . wp_get_attachment_image( $this->cover ) . wp_get_attachment_image( $this->image2 ),
			null,
			null,
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1_thumbnail, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][3]['role'] );
		$this->assertEquals( $image2_thumbnail, $result[1]['components'][3]['URL'] );
		$this->assertEquals( 4, count( $result[1]['components'] ) );

		/*
		 * Scenario 7:
		 * - No featured image is set.
		 * - Images in the content.
		 * - Caption set via postmeta.
		 * Expected: The first image from the content is set as the cover image and the first image from the content has been removed, but the caption from postmeta is used.
		 */
		$content = new Exporter_Content(
			1,
			'My Title',
			'<p>Hello, World!</p>' . wp_get_attachment_image( $this->cover ) . wp_get_attachment_image( $this->image2 ),
			null,
			[
				'caption' => 'Test caption from postmeta',
				'url'     => '',
			],
			'Author Name'
		);
		$builder = new Components( $content, $this->settings );
		$result = $builder->to_array();
		$this->assertEquals( 'header', $result[0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $result[0]['layout'] );
		$this->assertEquals( 'photo', $result[0]['components'][0]['role'] );
		$this->assertEquals( 'Test caption from postmeta', $result[0]['components'][0]['caption']['text'] );
		$this->assertEquals( 'headerPhotoLayoutWithCaption', $result[0]['components'][0]['layout'] );
		$this->assertEquals( $image1_thumbnail, $result[0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $result[1]['components'][3]['role'] );
		$this->assertEquals( $image2_thumbnail, $result[1]['components'][3]['URL'] );
		$this->assertEquals( 4, count( $result[1]['components'] ) );
	}

	/**
	 * Tests the functionality of the get_image_full_size_url function.
	 *
	 * @dataProvider dataImageFullSizeUrl
	 *
	 * @param string $original The original URL to test.
	 * @param string $expected The expected result.
	 *
	 * @throws ReflectionException
	 */
	public function testGetImageFullSizeUrl( $original, $expected ) {
		$class  = new ReflectionClass( 'Apple_Exporter\Builders\Components' );
		$method = $class->getMethod( 'get_image_full_size_url' );
		$method->setAccessible( true );
		$builder = new Components( $this->content, $this->settings );
		$this->assertEquals( $expected, $method->invokeArgs( $builder, [ $original ] ) );
	}

	/**
	 * Ensures that the specified component order is respected.
	 *
	 * @dataProvider dataMetaComponentOrdering
	 *
	 * @param array $order The meta component order setting to use.
	 * @param array $expected The expected component order after compilation.
	 * @param array $components The expected container components, in order.
	 *
	 * @access public
	 */
	public function testMetaComponentOrdering( $order, $expected, $components ) {

		// Setup.
		$theme = \Apple_Exporter\Theme::get_used();
		$settings = $theme->all_settings();
		$settings['enable_advertisement'] = 'no';
		$settings['meta_component_order'] = $order;
		$theme->load( $settings );
		$this->assertTrue( $theme->save() );
		$builder = new Components( $this->content, $this->settings );
		$result = $builder->to_array();

		// Test.
		for ( $i = 0; $i < count( $expected ); $i ++ ) {
			$this->assertEquals( $expected[ $i ], $result[ $i ]['role'] );
			if ( 'container' === $result[ $i ]['role'] ) {
				for ( $j = 0; $j < count( $components ); $j ++ ) {
					$this->assertEquals(
						$components[ $j ],
						$result[ $i ]['components'][ $j ]['role']
					);
				}
			}
		}
	}
}
