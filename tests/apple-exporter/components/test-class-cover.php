<?php
/**
 * Publish to Apple News tests: Cover_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Components\Cover;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Cover class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Cover_Test extends Component_TestCase {

	/**
	 * Tests the JSON generation for the Cover component when provided with a bare URL and image bundling.
	 */
	public function testGeneratedJSON() {
		$this->settings->set( 'use_remote_images', 'no' );

		$this->prophecized_workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldBeCalled();

		$component = new Cover(
			'http://someurl.com/filename.jpg',
			$this->prophecized_workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'   => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL'    => 'bundle://filename.jpg'
					)
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the JSON generation for the Cover component when provided with a bare URL and remote images.
	 */
	public function testGeneratedJSONRemoteImages() {
		$this->settings->set( 'use_remote_images', 'yes' );

		$this->prophecized_workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldNotBeCalled();

		$component = new Cover(
			'http://someurl.com/filename.jpg',
			$this->prophecized_workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'   => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL'    => 'http://someurl.com/filename.jpg'
					)
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the JSON generation for the Cover component when provided with image HTML but no caption.
	 */
	public function testGeneratedJSONFromHTMLNoCaption() {
		$this->settings->set( 'use_remote_images', 'yes' );

		// Create dummy post and attachment.
		$post_id = self::factory()->post->create();
		$image   = $this->get_new_attachment( $post_id );
		$this->set_workspace_post_id( $post_id );

		$component = new Cover(
			wp_get_attachment_url( $image ),
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'   => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL'    => wp_get_attachment_url( $image ),
					)
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the JSON generation for the Cover component when provided with image HTML and a caption.
	 */
	public function testGeneratedJSONFromHTMLWithCaption() {
		$this->settings->set( 'use_remote_images', 'yes' );
		$this->set_theme_settings( [ 'cover_caption' => true ] );

		// Create dummy post and attachment.
		$post_id = self::factory()->post->create();
		$image   = $this->get_new_attachment( $post_id );
		$this->set_workspace_post_id( $post_id );

		$component = new Cover(
			[
				'caption' => 'Test Caption',
				'url'     => wp_get_attachment_url( $image ),
			],
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'    => 'photo',
						'URL'     => wp_get_attachment_url( $image ),
						'layout'  => 'headerPhotoLayoutWithCaption',
						'caption' => array(
							'format'    => 'html',
							'text'      => 'Test Caption',
							'textStyle' => array(
								'fontName' => 'AvenirNext-Italic',
							),
						),
					),
					array(
						'role'      => 'caption',
						'text'      => 'Test Caption',
						'format'    => 'html',
						'textStyle' => array(
							'fontName'   => 'AvenirNext-Italic',
							'fontSize'   => 16,
							'tracking'   => 0,
							'lineHeight' => 24.0,
							'textColor'  => '#4f4f4f',
						),
					),
				),
				'behavior'   => array(
					'type'   => 'parallax',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}

	/**
	 * Tests the behavior of the `apple_news_cover_json` filter.
	 */
	public function testFilter() {
		$this->settings->set( 'use_remote_images', 'no' );

		$this->prophecized_workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldBeCalled();

		$component = new Cover(
			'http://someurl.com/filename.jpg',
			$this->prophecized_workspace->reveal(),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		add_filter(
			'apple_news_cover_json',
			function ( $json ) {
				$json['behavior']['type'] = 'background_motion';

				return $json;
			}
		);

		$this->assertEquals(
			array(
				'role'       => 'header',
				'layout'     => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'   => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL'    => 'bundle://filename.jpg'
					)
				),
				'behavior'   => array(
					'type'   => 'background_motion',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}

	/**
	 * Ensures that the lightbox font is set to the same font face as the image caption.
	 */
	public function testLightboxFont() {
		$this->set_theme_settings(
			[
				'caption_font'  => 'Menlo-Regular',
				'cover_caption' => true,
			]
		);

		// Create an image and give it a caption.
		$image_id = $this->get_new_attachment( 0, 'Test Caption!' );

		// Create a test post.
		$post_id = self::factory()->post->create();

		// Set the featured image for the post.
		set_post_thumbnail( $post_id, $image_id );

		// Ensure that the font set on the lightbox is the same as the font set on the caption above.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			'Menlo-Regular',
			$json['components'][0]['components'][0]['caption']['textStyle']['fontName']
		);
	}

	/**
	 * Ensures that the cover caption is not enabled by default, but can be
	 * enabled via a setting.
	 */
	public function testCaptionSetting() {
		// Create an image and give it a caption.
		$image_id = $this->get_new_attachment( 0, 'Test Caption!' );

		// Create a test post.
		$post_id = self::factory()->post->create();

		// Set the featured image for the post.
		set_post_thumbnail( $post_id, $image_id );

		// Ensure that the caption is not set on the Cover component by default.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( 1, count( $json['components'][0]['components'] ) );
		$this->assertEquals( 'headerPhotoLayout', $json['components'][0]['components'][0]['layout'] );

		// Enable support for the cover caption.
		$this->set_theme_settings( [ 'cover_caption' => true ] );

		// Ensure that the caption is set on the Cover component.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( 2, count( $json['components'][0]['components'] ) );
		$this->assertEquals( 'headerPhotoLayoutWithCaption', $json['components'][0]['components'][0]['layout'] );
		$this->assertEquals( 'caption', $json['components'][0]['components'][1]['role'] );
	}
}
