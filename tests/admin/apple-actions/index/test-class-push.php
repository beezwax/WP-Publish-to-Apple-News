<?php
/**
 * Publish to Apple News Tests: Admin_Action_Index_Push_Test class
 *
 * Contains a class to test the functionality of the Apple_Actions\Index\Push class.
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Actions\Action_Exception;

/**
 * A class used to test the functionality of the Apple_Actions\Index\Push class.
 */
class Admin_Action_Index_Push_Test extends Apple_News_Testcase {

	public function data_metadata() {
		return [
			[ 'apple_news_is_hidden', true, false, false, false ],
			[ 'apple_news_is_paid', false, true, false, false ],
			[ 'apple_news_is_preview', false, false, true, false ],
			[ 'apple_news_is_sponsored', false, false, false, true ],
		];
	}

	/**
	 * Tests the behavior of the component errors setting (none, warn, fail).
	 */
	public function test_component_errors() {
		// Set up a post with an invalid element (div).
		$this->become_admin();
		$user_id   = wp_get_current_user()->ID;
		$post_id_1 = self::factory()->post->create( [ 'post_author' => $user_id, 'post_content' => '<div>Test Content</div>' ] );

		// Test the default behavior, which is no warning or error.
		$this->get_request_for_post( $post_id_1 );
		$notices = get_user_meta( $user_id, 'apple_news_notice', true );
		$this->assertEquals( 2, count( $notices ) );
		$this->assertEquals( 'error', $notices[0]['type'] );
		$this->assertEquals( 'Your Apple News API settings seem to be empty. Please fill in the API key, API secret and API channel fields in the plugin configuration page.', $notices[0]['message'] );
		$this->assertEquals( 'success', $notices[1]['type'] );
		$this->assertEquals( 'abcd1234-ef56-ab78-cd90-efabcdef123456', get_post_meta( $post_id_1, 'apple_news_api_id', true ) );

		// Test the behavior of component warnings.
		$this->settings->component_alerts = 'warn';
		$post_id_2 = self::factory()->post->create( [ 'post_author' => $user_id, 'post_content' => '<div>Test Content</div>' ] );
		$this->get_request_for_post( $post_id_2 );
		$notices = get_user_meta( $user_id, 'apple_news_notice', true );
		$this->assertEquals( 4, count( $notices ) );
		$this->assertEquals( 'error', $notices[2]['type'] );
		$this->assertEquals( 'The following components are unsupported by Apple News and were removed: div', $notices[2]['message'] );
		$this->assertEquals( 'success', $notices[3]['type'] );
		$this->assertEquals( 'abcd1234-ef56-ab78-cd90-efabcdef123456', get_post_meta( $post_id_1, 'apple_news_api_id', true ) );

		// Test the behavior of component errors.
		$this->settings->component_alerts = 'fail';
		$post_id_3 = self::factory()->post->create( [ 'post_author' => $user_id, 'post_content' => '<div>Test Content</div>' ] );
		$exception = false;
		try {
			$this->get_request_for_post( $post_id_3 );
		} catch ( Action_Exception $e ) {
			$exception = $e;
		}
		$this->assertEquals( 'The following components are unsupported by Apple News and prevented publishing: div', $exception->getMessage() );
		$this->assertEquals( null, get_post_meta( $post_id_3, 'apple_news_api_id', true ) );

		// Clean up after ourselves.
		$this->settings->component_alerts = 'none';
	}

	/**
	 * Ensures that postmeta will be properly set after creating an article via
	 * the API.
	 */
	public function test_create() {
		$post_id = self::factory()->post->create();
		$this->get_request_for_post( $post_id );

		// Values in the assertions here are added in the get_request_for_post function call above.
		$this->assertEquals( 'abcd1234-ef56-ab78-cd90-efabcdef123456', get_post_meta( $post_id, 'apple_news_api_id', true ) );
		$this->assertEquals( '2020-01-02T03:04:05Z', get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
		$this->assertEquals( '2020-01-02T03:04:05Z', get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
		$this->assertEquals( 'https://apple.news/ABCDEFGHIJKLMNOPQRSTUVW', get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
		$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
	}

	/**
	 * Ensure the section is added to the metadata sent with the request.
	 */
	public function test_create_with_sections() {
		$post_id = self::factory()->post->create();
		add_post_meta( $post_id, 'apple_news_sections', [ 'https://news-api.apple.com/sections/123' ] );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );
		$this->assertEquals( [ 'https://news-api.apple.com/sections/123' ], $metadata['data']['links']['sections'] );
	}

	/**
	 * Ensures that custom metadata is properly set.
	 */
	public function test_custom_metadata() {
		$post_id  = self::factory()->post->create();
		$metadata = [
			[
				'key'   => 'isBoolean',
				'type'  => 'boolean',
				'value' => true,
			],
			[
				'key'   => 'isNumber',
				'type'  => 'number',
				'value' => 3,
			],
			[
				'key'   => 'isString',
				'type'  => 'string',
				'value' => 'Test String Value',
			],
			[
				'key'   => 'isArray',
				'type'  => 'array',
				'value' => '["a", "b", "c"]',
			],
		];
		add_post_meta( $post_id, 'apple_news_metadata', $metadata );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );

		// Ensure metadata was properly compiled into the request.
		$this->assertEquals( true, $metadata['data']['isBoolean'] );
		$this->assertEquals( 3, $metadata['data']['isNumber'] );
		$this->assertEquals( 'Test String Value', $metadata['data']['isString'] );
		$this->assertEquals( ['a', 'b', 'c'], $metadata['data']['isArray'] );
	}

	/**
	 * Ensures that maturity rating is properly set in the request.
	 */
	public function test_maturity_rating() {
		$post_id  = self::factory()->post->create();
		add_post_meta( $post_id, 'apple_news_maturity_rating', 'MATURE' );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );
		$this->assertEquals( 'MATURE', $metadata['data']['maturityRating'] );
	}

	/**
	 * Ensures that named metadata is properly set.
	 *
	 * @dataProvider data_metadata
	 *
	 * @param string $meta_key    The meta key to set to true (e.g., apple_news_is_hidden).
	 * @param bool   $isHidden    The expected value for isHidden in the request.
	 * @param bool   $isPaid      The expected value for isPaid in the request.
	 * @param bool   $isPreview   The expected value for isPreview in the request.
	 * @param bool   $isSponsored The expected value for isSponsored in the request.
	 */
	public function test_metadata( $meta_key, $isHidden, $isPaid, $isPreview, $isSponsored ) {
		$post_id  = self::factory()->post->create();
		add_post_meta( $post_id, $meta_key, true );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );

		// Check the values for the four metadata keys against expected values.
		$this->assertEquals( $isHidden, $metadata['data']['isHidden'] );
		$this->assertEquals( $isPaid, $metadata['data']['isPaid'] );
		$this->assertEquals( $isPreview, $metadata['data']['isPreview'] );
		$this->assertEquals( $isSponsored, $metadata['data']['isSponsored'] );
	}

	/**
	 * Tests skipping publish of a post by filters or by taxonomy term.
	 */
	public function test_skip() {
		$post_id = self::factory()->post->create();

		// Test the apple_news_skip_push filter.
		add_filter( 'apple_news_skip_push', '__return_true' );
		$exception = false;
		try {
			$this->get_request_for_post( $post_id );
		} catch ( Action_Exception $e ) {
			$exception = $e;
		}
		$this->assertEquals( sprintf( 'Skipped push of article %d due to the apple_news_skip_push filter.', $post_id ), $exception->getMessage() );
		remove_filter( 'apple_news_skip_push', '__return_true' );

		// Test the new filter for skipping by term ID.
		$term_id = self::factory()->term->create( [ 'taxonomy' => 'category' ] );
		wp_set_object_terms( $post_id, $term_id, 'category' );
		$skip_filter = function () use ( $term_id ) {
			return [ $term_id ];
		};
		add_filter( 'apple_news_skip_push_term_ids', $skip_filter );
		$exception = false;
		try {
			$this->get_request_for_post( $post_id );
		} catch ( Action_Exception $e ) {
			$exception = $e;
		}
		$this->assertEquals( sprintf( 'Skipped push of article %d due to the presence of a skip push taxonomy term.', $post_id ), $exception->getMessage() );
		remove_filter( 'apple_news_skip_push_term_ids', $skip_filter );

		// Test skip by setting the option for skipping by term ID.
		$this->settings->api_autosync_skip = wp_json_encode( [ $term_id ] );
		$exception = false;
		try {
			$this->get_request_for_post( $post_id );
		} catch ( Action_Exception $e ) {
			$exception = $e;
		}
		$this->assertEquals( sprintf( 'Skipped push of article %d due to the presence of a skip push taxonomy term.', $post_id ), $exception->getMessage() );
		$this->settings->api_autosync_skip = '';
	}

	/**
	 * Tests the update workflow to ensure that posts are only updated when
	 * changes have been made.
	 */
	public function test_update() {
		// Create a post and fake sending it to the API.
		$post = self::factory()->post->create_and_get();
		$this->get_request_for_post( $post->ID );

		// Ensure that the fake response from the API was saved to postmeta.
		$this->assertEquals( 'abcd1234-ef56-ab78-cd90-efabcdef123456', get_post_meta( $post->ID, 'apple_news_api_id', true ) );
		$this->assertEquals( '2020-01-02T03:04:05Z', get_post_meta( $post->ID, 'apple_news_api_created_at', true ) );
		$this->assertEquals( '2020-01-02T03:04:05Z', get_post_meta( $post->ID, 'apple_news_api_modified_at', true ) );
		$this->assertEquals( 'https://apple.news/ABCDEFGHIJKLMNOPQRSTUVW', get_post_meta( $post->ID, 'apple_news_api_share_url', true ) );
		$this->assertEquals( null, get_post_meta( $post->ID, 'apple_news_api_deleted', true ) );

		// Try to sync the post again, and verify that it bails out before attempting the sync.
		$exception = false;
		try {
			$this->get_request_for_post( $post->ID );
		} catch ( Action_Exception $e ) {
			$exception = $e;
		}
		$this->assertEquals( 'Skipped push of article ' . $post->ID . ' to Apple News because it is already in sync.', $exception->getMessage() );

		// Update the post by changing the title and ensure that the update is sent to Apple.
		$post->post_title = 'Test New Title';
		wp_update_post( $post );
		$request = $this->get_request_for_update( $post->ID );
		$body    = $this->get_body_from_request( $request );
		$this->assertEquals( 'Test New Title', $body['title'] );
	}
}
