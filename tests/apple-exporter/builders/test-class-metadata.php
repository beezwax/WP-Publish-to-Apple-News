<?php
/**
 * Publish to Apple News Tests: Metadata_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Exporter_Content;
use Apple_Exporter\Builders\Metadata;

/**
 * A class which is used to test the Apple_Exporter\Builders\Metadata class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Metadata_Test extends Apple_News_Testcase {

	/**
	 * Ensures that metadata is properly set.
	 */
	public function test_metadata() {
		// Setup.
		$post_id = self::factory()->post->create(
			[
				'post_content' => '<p>Hello, World!</p>',
				'post_date'    => '2016-04-01 00:00:00',
				'post_excerpt' => 'Sample excerpt.',
				'post_title'   => 'My Title',
			]
		);
		$image   = $this->get_new_attachment( $post_id );
		set_post_thumbnail( $post_id, $image );
		$result   = $this->get_json_for_post( $post_id );
		$metadata = $result['metadata'];

		// Assertions.
		$this->assertEquals( '2016-04-01T00:00:00+00:00', $metadata['dateCreated'] );
		$this->assertEquals( '2016-04-01T00:00:00+00:00', $metadata['dateModified'] );
		$this->assertEquals( '2016-04-01T00:00:00+00:00', $metadata['datePublished'] );
		$this->assertEquals( 'Sample excerpt.', $metadata['excerpt'] );
		$this->assertEquals( wp_get_attachment_url( $image ), $metadata['thumbnailURL'] );
	}

	/**
	 * Ensures video metadata is properly added.
	 *
	 * @access public
	 */
	public function test_video() {
		// Setup.
		$post_id  = self::factory()->post->create(
			[
				'post_content' => '<figure class="wp-block-video"><video controls="" poster="https://example.com/wp-content/uploads/2017/02/example-poster.jpg" src="https://example.com/wp-content/uploads/2017/02/example-video.mp4"></video></figure>',
			]
		);
		$result   = $this->get_json_for_post( $post_id );
		$metadata = $result['metadata'];

		// Assertions.
		$this->assertEquals(
			'https://example.com/wp-content/uploads/2017/02/example-poster.jpg',
			$metadata['thumbnailURL']
		);
		$this->assertEquals(
			'https://example.com/wp-content/uploads/2017/02/example-video.mp4',
			$metadata['videoURL']
		);
	}
}
