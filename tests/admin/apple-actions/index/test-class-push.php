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

// TODO: REMOVE THESE
use Apple_Actions\Index\Push as Push;
use Prophecy\Argument as Argument;

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

	// TODO: REFACTOR LINE.

	/**
	 * A filter callback to simulate a JSON error.
	 *
	 * @access public
	 * @return array An array containing a JSON error.
	 */
	public function filterAppleNewsGetErrors() {
		return array(
			array(
				'json_errors' => array(
					'Test JSON error.',
				),
			),
		);
	}

	public function testComponentErrorsNone() {
		$this->settings->set( 'component_alerts', 'none' );

		$response = $this->dummy_response();
		$api = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->post_article_to_channel( Argument::cetera() )
			->willReturn( $response )
			->shouldBeCalled();

		// We need to create an iframe, so run as administrator.
		$this->become_admin();

		// Create post
		$post_id = $this->factory->post->create( array(
			'post_content' => '<p><iframe width="460" height="460" src="http://unsupportedservice.com/embed.html?video=1232345&autoplay=0" frameborder="0" allowfullscreen></iframe></p>',
		) );

		$action = new Push( $this->settings, $post_id );
		$action->set_api( $api->reveal() );
		$action->perform();

		// The post was still quietly sent to Apple News despite the removal of the iframe
		$this->assertEquals( $response->data->id, get_post_meta( $post_id, 'apple_news_api_id', true ) );
		$this->assertEquals( $response->data->createdAt, get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
		$this->assertEquals( $response->data->modifiedAt, get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
		$this->assertEquals( $response->data->shareUrl, get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
		$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
	}

	public function testComponentErrorsWarn() {
		$this->settings->set( 'component_alerts', 'warn' );
		$this->settings->set( 'json_alerts', 'none' );

		$response = $this->dummy_response();
		$api = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->post_article_to_channel( Argument::cetera() )
			->willReturn( $response )
			->shouldBeCalled();

		// We need to create a nonsense HTML element, so run as administrator.
		$user_id = $this->set_admin();

		// Create post
		$post_id = $this->factory->post->create( array(
			'post_content' => '<p><invalidelement src="http://unsupportedservice.com/embed.html?video=1232345&autoplay=0"></invalidelement></p>',
		) );

		$action = new Push( $this->settings, $post_id );
		$action->set_api( $api->reveal() );
		$action->perform();

		// An admin error notice was created
		$notices = get_user_meta( $user_id, 'apple_news_notice', true );
		$this->assertNotEmpty( $notices );

		array_pop( $notices );
		$component_notice = end( $notices );
		$this->assertEquals( 'The following components are unsupported by Apple News and were removed: invalidelement', $component_notice['message'] );

		// The post was still sent to Apple News
		$this->assertEquals( $response->data->id, get_post_meta( $post_id, 'apple_news_api_id', true ) );
		$this->assertEquals( $response->data->createdAt, get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
		$this->assertEquals( $response->data->modifiedAt, get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
		$this->assertEquals( $response->data->shareUrl, get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
		$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
	}

	public function testComponentErrorsFail() {
		$this->settings->set( 'component_alerts', 'fail' );
		$this->settings->set( 'json_alerts', 'none' );

		$response = $this->dummy_response();
		$api = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->post_article_to_channel( Argument::cetera() )
			->willReturn( $response )
			->shouldNotBeCalled();

		// We need to create a nonsense HTML element, so run as administrator.
		$user_id = $this->set_admin();

		// Create post
		$post_id = $this->factory->post->create( array(
			'post_content' => '<p><invalidelement src="http://unsupportedservice.com/embed.html?video=1232345&autoplay=0"></invalidelement></p>',
		) );

		$action = new Push( $this->settings, $post_id );
		$action->set_api( $api->reveal() );

		try {
			$action->perform();
		} catch ( Action_Exception $e ) {

			// An admin error notice was created
			$notices = get_user_meta( $user_id, 'apple_news_notice', true );
			$this->assertNotEmpty( $notices );

			$component_notice = end( $notices );
			$this->assertEquals( 'The following components are unsupported by Apple News and prevented publishing: invalidelement', $e->getMessage() );

			// The post was not sent to Apple News
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_id', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
		}
	}

	public function testJSONErrorsWarn() {
		$this->settings->set( 'component_alerts', 'none' );
		$this->settings->set( 'json_alerts', 'warn' );

		$response = $this->dummy_response();
		$api = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->post_article_to_channel( Argument::cetera() )
			->willReturn( $response )
			->shouldBeCalled();

		// We need to create an iframe, so run as administrator
		$user_id = $this->set_admin();

		// Create post
		$post_id = $this->factory->post->create( array(
			'post_content' => 'Test post content',
		) );

		// Manually add a JSON error to the postmeta via a filter.
		add_filter(
			'apple_news_get_errors',
			array( $this, 'filterAppleNewsGetErrors' )
		);

		$action = new Push( $this->settings, $post_id );
		$action->set_api( $api->reveal() );
		$action->perform();

		// An admin error notice was created
		$notices = get_user_meta( $user_id, 'apple_news_notice', true );
		$notice_messages = wp_list_pluck( $notices, 'message' );
		$this->assertTrue( in_array(
			'The following JSON errors were detected when publishing to Apple News: Test JSON error.',
			$notice_messages,
			true
		) );

		// The post was still sent to Apple News
		$this->assertEquals( $response->data->id, get_post_meta( $post_id, 'apple_news_api_id', true ) );
		$this->assertEquals( $response->data->createdAt, get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
		$this->assertEquals( $response->data->modifiedAt, get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
		$this->assertEquals( $response->data->shareUrl, get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
		$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );

		// Remove the filter.
		remove_filter(
			'apple_news_get_errors',
			array( $this, 'filterAppleNewsGetErrors' )
		);
	}

	public function testJSONErrorsFail() {
		$this->settings->set( 'component_alerts', 'none' );
		$this->settings->set( 'json_alerts', 'fail' );

		$response = $this->dummy_response();
		$api = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->post_article_to_channel( Argument::cetera() )
			->willReturn( $response )
			->shouldNotBeCalled();

		// We need to create an iframe, so run as administrator
		$user_id = $this->set_admin();

		// Create post
		$post_id = $this->factory->post->create( array(
			'post_content' => 'Test post content.',
		) );

		// Manually add a JSON error to the postmeta via a filter.
		add_filter(
			'apple_news_get_errors',
			array( $this, 'filterAppleNewsGetErrors' )
		);

		$action = new Push( $this->settings, $post_id );
		$action->set_api( $api->reveal() );

		try {
			$action->perform();
		} catch ( Action_Exception $e ) {
			$this->assertEquals( 'The following JSON errors were detected and prevented publishing to Apple News: Test JSON error.', $e->getMessage() );

			// The post was not sent to Apple News
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_id', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_created_at', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_modified_at', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_share_url', true ) );
			$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
		}

		// Remove the filter.
		remove_filter(
			'apple_news_get_errors',
			array( $this, 'filterAppleNewsGetErrors' )
		);
	}
}
