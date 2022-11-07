<?php
/**
 * Publish to Apple News tests: Apple_News_Admin_Action_Index_Delete_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Actions\Index\Delete;

/**
 * A class to test the functionality of the Apple_Actions\Index\Delete class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Apple_News_Admin_Action_Index_Delete_Test extends Apple_News_Testcase {
	/**
	 * Tests the behavior of the automatic delete setting.
	 */
	public function test_auto_delete() {
		// Create a post, which will automatically be published.
		$this->become_admin();
		$this->add_http_response( 'POST', 'https://news-api.apple.com/channels/foo/articles', wp_json_encode( $this->fake_article_response() ) );
		$post_id = self::factory()->post->create();

		// Add an HTTP response for the delete operation, then delete the article, and verify it was triggered.
		$this->add_http_response( 'DELETE', 'https://news-api.apple.com/articles/abcd1234-ef56-ab78-cd90-efabcdef123456' );
		wp_delete_post( $post_id, true );
		$this->assertEmpty( $this->http_responses['DELETE']['https://news-api.apple.com/articles/abcd1234-ef56-ab78-cd90-efabcdef123456'] );
	}
}
