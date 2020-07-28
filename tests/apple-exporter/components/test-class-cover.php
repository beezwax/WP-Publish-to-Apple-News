<?php
/**
 * Publish to Apple News Tests: Cover_Test class
 *
 * Contains a class which is used to test Apple_Exporter\Components\Cover.
 *
 * @package Apple_News
 * @subpackage Tests
 */

require_once __DIR__ . '/class-component-testcase.php';

use Apple_Exporter\Components\Cover;
use Apple_Exporter\Workspace;

/**
 * A class which is used to test the Apple_Exporter\Components\Cover class.
 */
class Cover_Test extends Component_TestCase {

	/**
	 * Tests the JSON generation for the Cover component when provided with a bare URL and image bundling.
	 */
	public function testGeneratedJSON() {
		$this->settings->set( 'use_remote_images', 'no' );

		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified params
		$workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldBeCalled();

		$component = new Cover( 'http://someurl.com/filename.jpg',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'header',
				'layout' => 'headerPhotoLayout',
				'components' => array(
					array(
						'role' => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL' => 'bundle://filename.jpg'
						)
					),
				'behavior' => array(
					'type' => 'parallax',
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
		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified params
		$workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldNotBeCalled();

		$component = new Cover( 'http://someurl.com/filename.jpg',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'header',
				'layout' => 'headerPhotoLayout',
				'components' => array(
					array(
						'role' => 'photo',
						'layout' => 'headerPhotoLayout',
						'URL' => 'http://someurl.com/filename.jpg'
					)
				),
				'behavior' => array(
					'type' => 'parallax',
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
		$file    = dirname( dirname( __DIR__ ) ) . '/data/test-image.jpg';
		$post_id = self::factory()->post->create();
		$image   = self::factory()->attachment->create_upload_object( $file, $post_id );

		$component = new Cover(
			wp_get_attachment_url( $image ),
			new Workspace( $post_id ),
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

		// Create dummy post and attachment.
		$file    = dirname( dirname( __DIR__ ) ) . '/data/test-image.jpg';
		$post_id = self::factory()->post->create();
		$image   = self::factory()->attachment->create_upload_object( $file, $post_id );

		$component = new Cover(
			[
				'caption' => 'Test Caption',
				'url'     => wp_get_attachment_url( $image ),
			],
			new Workspace( $post_id ),
			$this->settings,
			$this->styles,
			$this->layouts
		);

		$this->assertEquals(
			array(
				'role' => 'header',
				'layout' => 'headerPhotoLayout',
				'components' => array(
					array(
						'role'    => 'photo',
						'URL'     => wp_get_attachment_url( $image ),
						'layout'  => 'headerPhotoLayoutWithCaption',
						'caption' => array(
							'format' => 'html',
							'text'   => 'Test Caption',
						),
					),
					array(
						'role'      => 'caption',
						'text'      => 'Test Caption',
						'format'    => 'html',
						'textStyle' => array(
							'fontName' => 'AvenirNext-Italic',
              'fontSize' => 16,
              'tracking' => 0,
              'lineHeight' => 24.0,
              'textColor' => '#4f4f4f',
						),
					),
				),
				'behavior' => array(
					'type' => 'parallax',
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

		$workspace = $this->prophet->prophesize( '\Apple_Exporter\Workspace' );
		// get_file_contents and write_tmp_files must be caleld with the specified params
		$workspace->bundle_source( 'filename.jpg', 'http://someurl.com/filename.jpg' )->shouldBeCalled();

		$component = new Cover( 'http://someurl.com/filename.jpg',
			$workspace->reveal(), $this->settings, $this->styles, $this->layouts );

		add_filter( 'apple_news_cover_json', function( $json ) {
			$json['behavior']['type'] = 'background_motion';
			return $json;
		} );

		$this->assertEquals(
			array(
				'role' => 'header',
				'layout' => 'headerPhotoLayout',
				'components' => array( array(
					'role' => 'photo',
					'layout' => 'headerPhotoLayout',
					'URL' => 'bundle://filename.jpg'
				) ),
				'behavior' => array(
					'type' => 'background_motion',
					'factor' => 0.8
				),
			),
			$component->to_array()
		);
	}
}
