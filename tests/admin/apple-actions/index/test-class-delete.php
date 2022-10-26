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
	 * Tests the behavior of the delete action.
	 */
	public function test_action_perform() {
		$remote_id = uniqid();
		$api       = $this->prophet->prophesize( '\Apple_Push_API\API' );
		$api->delete_article( $remote_id )
			->shouldBeCalled();

		// Create post with dummy remote id.
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, 'apple_news_api_id', $remote_id );

		$action = new Delete( $this->settings, $post_id );
		$action->set_api( $api->reveal() );
		$action->perform();

		$this->assertNotEquals( null, get_post_meta( $post_id, 'apple_news_api_deleted', true ) );
		$this->assertEquals( null, get_post_meta( $post_id, 'apple_news_api_id', true ) );
	}

	/**
	 * Tests the behavior of the action when the post was not pushed to Apple News.
	 */
	public function test_action_perform_when_not_pushed() {
		// Expect an exception.
		$this->setExpectedException( '\Apple_Actions\Action_Exception', 'This post has not been pushed to Apple News, cannot delete.' );

		$api     = $this->prophet->prophesize( '\Push_API\API' );
		$post_id = $this->factory->post->create();

		$action = new Delete( $this->settings, $post_id );
		$action->set_api( $api->reveal() );
		$action->perform();
	}
}
