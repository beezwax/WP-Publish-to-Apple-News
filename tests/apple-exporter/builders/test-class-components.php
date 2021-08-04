<?php
/**
 * Publish to Apple News Tests: Component_Tests class
 *
 * Contains a class which is used to test \Apple_Exporter\Builders\Components.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use \Apple_Exporter\Builders\Components;

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
	 * Tests the ability to view captions below cover images.
	 */
	public function testCoverImage() {
		// Enable the cover caption option in the theme.
		$this->set_theme_settings( [ 'cover_caption' => true ] );

		// Create a new post and set an image with a caption as the featured image.
		$post_id = self::factory()->post->create();
		$image   = $this->get_new_attachment( $post_id, 'Test Caption', 'Test alt text' );
		set_post_thumbnail( $post_id, $image );

		// Ensure that the caption carries through to the export.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'caption', $json['components'][0]['components'][1]['role'] );
		$this->assertEquals( 'Test Caption', $json['components'][0]['components'][1]['text'] );

		// Create a new post with an image with a caption in the content.
		$image_2   = $this->get_new_attachment( 0, 'Test Caption 2', 'Test alt text 2' );
		$post_id_2 = self::factory()->post->create( [ 'post_content' => $this->get_image_with_caption( $image_2 ) ] );

		// Ensure that the caption carries through to the export.
		$json_2 = $this->get_json_for_post( $post_id_2 );
		$this->assertEquals( 'caption', $json_2['components'][0]['components'][1]['role'] );
		$this->assertEquals( 'Test Caption 2', $json_2['components'][0]['components'][1]['text'] );
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

		// Get two images.
		$image_1 = $this->get_new_attachment();
		$image_2 = $this->get_new_attachment();

		/*
		 * Scenario 1:
		 * - No featured image is set.
		 * - No images in the content.
		 * Expected: No cover image is set.
		 */
		$post_1 = self::factory()->post->create();
		$json_1 = $this->get_json_for_post( $post_1 );
		$this->assertNotEquals( 'headerPhotoLayout', $json_1['components'][0]['layout'] );

		/*
		 * Scenario 2:
		 * - A featured image is set.
		 * - No images in the content.
		 * Expected: The featured image is set as the cover image.
		 */
		$post_2 = self::factory()->post->create();
		set_post_thumbnail( $post_2, $image_1 );
		$json_2 = $this->get_json_for_post( $post_2 );
		$this->assertEquals( 'header', $json_2['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_2['components'][0]['layout'] );
		$this->assertEquals( 'photo', $json_2['components'][0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_2['components'][0]['components'][0]['layout'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_2['components'][0]['components'][0]['URL'] );

		/*
		 * Scenario 3:
		 * - A featured image is set.
		 * - Image in the content, but not the same one as the featured image.
		 * Expected: The featured image is set as the cover image and the body image is still in the body.
		 */
		$post_3 = self::factory()->post->create( [ 'post_content' => wp_get_attachment_image( $image_2, 'full' ) ] );
		set_post_thumbnail( $post_3, $image_1 );
		$json_3 = $this->get_json_for_post( $post_3 );
		$this->assertEquals( 'header', $json_3['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_3['components'][0]['layout'] );
		$this->assertEquals( 'photo', $json_3['components'][0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_3['components'][0]['components'][0]['layout'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_3['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $json_3['components'][1]['components'][2]['role'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_2, 'full' ), $json_3['components'][1]['components'][2]['URL'] );

		/*
		 * Scenario 4:
		 * - A featured image is set.
		 * - Images in the content, including the same one as the featured image, but the featured image is not first.
		 * Expected: The featured image is set as the cover image and the body image is still in the body.
		 */
		$post_4 = self::factory()->post->create( [ 'post_content' => wp_get_attachment_image( $image_2, 'full' ) . wp_get_attachment_image( $image_1, 'full' ) ] );
		set_post_thumbnail( $post_4, $image_1 );
		$json_4 = $this->get_json_for_post( $post_4 );
		$this->assertEquals( 'header', $json_4['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_4['components'][0]['layout'] );
		$this->assertEquals( 'photo', $json_4['components'][0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_4['components'][0]['components'][0]['layout'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_4['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $json_4['components'][1]['components'][2]['role'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_2, 'full' ), $json_4['components'][1]['components'][2]['URL'] );
		$this->assertEquals( 'photo', $json_4['components'][1]['components'][3]['role'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_4['components'][1]['components'][3]['URL'] );

		/*
		 * Scenario 5:
		 * - A featured image is set.
		 * - Images in the content, including the same one as the featured image, and the featured image is first.
		 * Expected: The first image from the content is set as the cover image and the first image from the content has been removed. The featured image is ignored.
		 */
		$post_5 = self::factory()->post->create( [ 'post_content' => wp_get_attachment_image( $image_1, 'full' ) . wp_get_attachment_image( $image_2, 'full' ) ] );
		set_post_thumbnail( $post_5, $image_1 );
		$json_5 = $this->get_json_for_post( $post_5 );
		$this->assertEquals( 'header', $json_5['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_5['components'][0]['layout'] );
		$this->assertEquals( 'photo', $json_5['components'][0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_5['components'][0]['components'][0]['layout'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_5['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $json_5['components'][1]['components'][2]['role'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_2, 'full' ), $json_5['components'][1]['components'][2]['URL'] );
		$this->assertEquals( 3, count( $json_5['components'][1]['components'] ) );

		/*
		 * Scenario 6:
		 * - No featured image is set.
		 * - Images in the content.
		 * Expected: The first image from the content is set as the cover image and the first image from the content has been removed.
		 */
		$post_6 = self::factory()->post->create( [ 'post_content' => wp_get_attachment_image( $image_1, 'full' ) . wp_get_attachment_image( $image_2, 'full' ) ] );
		$json_6 = $this->get_json_for_post( $post_6 );
		$this->assertEquals( 'header', $json_6['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_6['components'][0]['layout'] );
		$this->assertEquals( 'photo', $json_6['components'][0]['components'][0]['role'] );
		$this->assertEquals( 'headerPhotoLayout', $json_6['components'][0]['components'][0]['layout'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_1, 'full' ), $json_6['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'photo', $json_6['components'][1]['components'][2]['role'] );
		$this->assertEquals( wp_get_attachment_image_url( $image_2, 'full' ), $json_6['components'][1]['components'][2]['URL'] );
		$this->assertEquals( 3, count( $json_6['components'][1]['components'] ) );
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
		$builder = new Components( $this->content, $this->content_settings );
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
		$this->set_theme_settings(
			[
				'enable_advertisement' => 'no',
				'meta_component_order' => $order,
			]
		);

		// Make a post with a featured image and get the JSON for it.
		$post_id = self::factory()->post->create();
		$image   = $this->get_new_attachment( $post_id );
		set_post_thumbnail( $post_id, $image );
		$json = $this->get_json_for_post( $post_id );

		// Test.
		for ( $i = 0; $i < count( $expected ); $i ++ ) {
			$this->assertEquals( $expected[ $i ], $json['components'][ $i ]['role'] );
			if ( 'container' === $json['components'][ $i ]['role'] ) {
				for ( $j = 0; $j < count( $components ); $j ++ ) {
					$this->assertEquals(
						$components[ $j ],
						$json['components'][ $i ]['components'][ $j ]['role']
					);
				}
			}
		}
	}
}
