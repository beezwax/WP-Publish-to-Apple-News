<?php
/**
 * Publish to Apple News tests: Admin_Action_Index_Export_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use \Apple_Actions\Index\Export as Export;

/**
 * A class to test the functionality of the Apple_Actions\Index\Export class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Admin_Action_Index_Export_Test extends Apple_News_Testcase {

	/**
	 * Returns an array of arrays representing function arguments to the
	 * testBrightcoveVideo function.
	 */
	public function dataProviderBrightcoveVideo() {
		return [
			[
				'<!-- wp:bc/brightcove {"account_id":"1234567890","player_id":"abcd1234-ef56-ab78-cd90-efa1234567890","video_id":"1234567890123","playlist_id":"","experience_id":"","video_ids":"","embed":"in-page","autoplay":"","playsinline":"","picture_in_picture":"","height":"100%","width":"100%","min_width":"0px","max_width":"640px","padding_top":"56%"} /-->',
			],
			[
				'[bc_video video_id="1234567890123" account_id="1234567890" player_id="abcd1234-ef56-ab78-cd90-efa1234567890" embed="in-page" padding_top="56%" autoplay="" min_width="0px" playsinline="" picture_in_picture="" max_width="640px" mute="" width="100%" height="100%" ]',
			],
		];
	}

	/**
	 * A filter to ensure that the is_exporting flag is set during export.
	 *
	 * @access public
	 * @return string The filtered content.
	 */
	public function filterTheContentTestIsExporting() {
		return apple_news_is_exporting() ? 'is exporting' : 'is not exporting';
	}

	/**
	 * Tests Brightcove video support.
	 *
	 * @param string $post_content The post content to load for the test.
	 *
	 * @dataProvider dataProviderBrightcoveVideo
	 */
	public function testBrightcoveVideo( $post_content ) {
		$post_id = self::factory()->post->create(
			[
				'post_content' => $post_content,
			]
		);
		$json    = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'video', $json['components'][2]['role'] );
		$this->assertEquals( 'https://edge.api.brightcove.com/playback/v1/accounts/1234567890/videos/1234567890123', $json['components'][2]['URL'] );
		$this->assertEquals( 'https://cf-images.us-east-1.prod.boltdns.net/v1/jit/1234567890/abcd1234-ef56-ab78-cd90-efabcd123456/main/1280x720/1s234ms/match/image.jpg', $json['components'][2]['stillURL'] );
	}

	/**
	 * Tests the ability to include a caption with a cover image.
	 */
	public function testCoverWithCaption() {
		$this->set_theme_settings( [ 'cover_caption' => true ] );

		// Create dummy post and attachment.
		$post_id = self::factory()->post->create();
		$image   = $this->get_new_attachment( $post_id, 'Test Caption' );

		// Set the image as the featured image for the post.
		set_post_thumbnail( $post_id, $image );

		// Run the export and check the result.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'photo', $json['components'][0]['components'][0]['role'] );
		$this->assertEquals( wp_get_attachment_url( $image ), $json['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'Test Caption', $json['components'][0]['components'][0]['caption']['text'] );
		$this->assertEquals( 'caption', $json['components'][0]['components'][1]['role'] );
		$this->assertEquals( 'Test Caption', $json['components'][0]['components'][1]['text'] );

		// Set cover image and caption via postmeta and ensure it takes priority.
		$image2 = $this->get_new_attachment();
		update_post_meta( $post_id, 'apple_news_coverimage', $image2 );
		update_post_meta( $post_id, 'apple_news_coverimage_caption', 'Test Caption 2' );
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'photo', $json['components'][0]['components'][0]['role'] );
		$this->assertEquals( wp_get_attachment_url( $image2 ), $json['components'][0]['components'][0]['URL'] );
		$this->assertEquals( 'Test Caption 2', $json['components'][0]['components'][0]['caption']['text'] );
		$this->assertEquals( 'caption', $json['components'][0]['components'][1]['role'] );
		$this->assertEquals( 'Test Caption 2', $json['components'][0]['components'][1]['text'] );
	}

	public function testHasExcerpt() {
		$title = 'My Title';
		$excerpt = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue...';

		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => '',
			'post_excerpt' => $excerpt,
		) );

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue...', $exporter_content->intro() );
	}

	public function testNoExcerpt() {
		$title = 'My Title';
		$content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue arcu. Curabitur lacus elit, venenatis a laoreet sit amet, imperdiet ac lorem. Curabitur sed leo sed ligula tempor feugiat. Cras in tellus et elit volutpat.</p>';

		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_excerpt' => '',
		) );

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals( '', $exporter_content->intro() );
	}

	public function testShortcodeInExcerpt() {
		$title = 'My Title';
		$content = '<p>[caption id="attachment_12345" align="aligncenter" width="500"]Test[/caption]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue arcu. Curabitur lacus elit, venenatis a laoreet sit amet, imperdiet ac lorem. Curabitur sed leo sed ligula tempor feugiat. Cras in tellus et elit volutpat.</p>';

		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_excerpt' => '',
		) );

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals( '', $exporter_content->intro() );
	}

	public function testBylineFormat() {
		$user_id = $this->factory->user->create( array(
			'role' => 'administrator',
			'display_name' => 'Testuser',
		) );

		$title = 'My Title';
		$content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue arcu. Curabitur lacus elit, venenatis a laoreet sit amet, imperdiet ac lorem. Curabitur sed leo sed ligula tempor feugiat. Cras in tellus et elit volutpat.</p>';

		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_excerpt' => '',
			'post_author' => $user_id,
			'post_date' => '2016-08-26 12:00',
		) );

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals( 'by Testuser | Aug 26, 2016 | 12:00 PM', $exporter_content->byline() );
	}

	public function testBylineFormatWithHashtag() {
		$user_id = $this->factory->user->create( array(
			'role' => 'administrator',
			'display_name' => '#Testuser',
		) );

		$title = 'My Title';
		$content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue arcu. Curabitur lacus elit, venenatis a laoreet sit amet, imperdiet ac lorem. Curabitur sed leo sed ligula tempor feugiat. Cras in tellus et elit volutpat.</p>';

		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_excerpt' => '',
			'post_author' => $user_id,
			'post_date' => '2016-08-26 12:00',
		) );

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals( 'by #Testuser | Aug 26, 2016 | 12:00 PM', $exporter_content->byline() );
	}

	public function testRemoveEntities() {
		$post_id = $this->factory->post->create( array(
			'post_title' => 'Test Title',
			'post_content' => '<p>&amp;Lorem ipsum dolor sit amet &amp; consectetur adipiscing elit.&amp;</p>',
			'post_date' => '2016-08-26 12:00',
		) );

		// Set HTML content format.
		$this->settings->html_support = 'yes';

		$export = new Export( $this->settings, $post_id );
		$exporter = $export->fetch_exporter();
		$exporter_content = $exporter->get_content();

		$this->assertEquals(
			'<p>&amp;Lorem ipsum dolor sit amet &amp; consectetur adipiscing elit.&amp;</p>',
			str_replace( array( "\n","\r" ), '', $exporter_content->content() )
		);

		// Set Markdown content format.
		$this->settings->html_support = 'no';

		$markdown_export = new Export( $this->settings, $post_id );
		$markdown_exporter = $markdown_export->fetch_exporter();
		$markdown_exporter_content = $markdown_exporter->get_content();
		$this->assertEquals(
			'<p>&Lorem ipsum dolor sit amet & consectetur adipiscing elit.&</p>',
			str_replace( array( "\n","\r" ), '', $markdown_exporter_content->content() )
		);
	}

	/**
	 * Tests mapping taxonomy terms to Apple News sections.
	 */
	public function test_section_mapping() {

		// Create a post.
		$post_id = self::factory()->post->create();

		// Create a term and add it to the post.
		$term_id = self::factory()->term->create(
			[
				'name'     => 'news',
				'taxonomy' => 'category',
			]
		);
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );

		// Create a taxonomy map.
		update_option(
			\Admin_Apple_Sections::TAXONOMY_MAPPING_KEY,
			[
				'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee' => [],
				'abcdef01-2345-6789-abcd-ef0123567890' => [ $term_id ],
				'bcdef012-3456-7890-abcd-ef0123567890' => [],
			]
		);

		// Cache as a transient to bypass the API call.
		set_transient(
			'apple_news_sections',
			[
				(object) [
					'createdAt'  => '2017-01-01T00:00:00Z',
					'id'         => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
					'isDefault'  => true,
					'links'      => (object) [
						'channel' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789a',
						'self'    => 'https://news-api.apple.com/channels/aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
					],
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name'       => 'Main',
					'shareUrl'   => 'https://apple.news/AAAAAAAAAA-BBBBBBBBBBBB',
					'type'       => 'section',
				],
				(object) [
					'createdAt'  => '2017-01-01T00:00:00Z',
					'id'         => 'abcdef01-2345-6789-abcd-ef0123567890',
					'isDefault'  => false,
					'links'      => (object) [
						'channel' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789a',
						'self'    => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890',
					],
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name'       => 'News',
					'shareUrl'   => 'https://apple.news/AbCdEfGhIj-KlMnOpQrStUv',
					'type'       => 'section',
				],
				(object) [
					'createdAt'  => '2017-01-01T00:00:00Z',
					'id'         => 'bcdef012-3456-7890-abcd-ef0123567890',
					'isDefault'  => false,
					'links'      => (object) [
						'channel' => 'https://news-api.apple.com/channels/bcdef012-3456-7890-abcd-ef012356789a',
						'self'    => 'https://news-api.apple.com/channels/bcdef012-3456-7890-abcd-ef0123567890',
					],
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name'       => 'Opinion',
					'shareUrl'   => 'https://apple.news/bCdEfGhIjK-lMnOpQrStUvW',
					'type'       => 'section',
				],
			]
		);

		// Get sections for the post.
		$sections = \Admin_Apple_Sections::get_sections_for_post( $post_id );

		// Check that the correct mapping was returned.
		$this->assertEquals(
			$sections,
			[ 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890' ]
		);

		// Add an empty value for postmeta for manual section mapping.
		add_post_meta( $post_id, 'apple_news_sections', [] );

		// Ensure that the automatic section mapping works correctly.
		$sections = \Admin_Apple_Sections::get_sections_for_post( $post_id );
		$this->assertEquals(
			$sections,
			[ 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890' ]
		);

		// Set a manual mapping and ensure that it works properly.
		update_post_meta( $post_id, 'apple_news_sections', [ 'https://news-api.apple.com/channels/bcdef012-3456-7890-abcd-ef0123567890' ] );
		$sections = \Admin_Apple_Sections::get_sections_for_post( $post_id );
		$this->assertEquals(
			$sections,
			[ 'https://news-api.apple.com/channels/bcdef012-3456-7890-abcd-ef0123567890' ]
		);

		// Remove the transient and the map.
		delete_option( \Admin_Apple_Sections::TAXONOMY_MAPPING_KEY );
		delete_transient( 'apple_news_sections' );
	}

	/**
	 * Tests the behavior of theme mapping by ensuring that a post with a
	 * category that is mapped to a particular section also gets the theme
	 * that is mapped to that section.
	 */
	public function testThemeMapping() {

		// Load an additional example theme to facilitate mapping.
		$this->load_example_theme( 'colorful' );

		// Ensure the default theme is active.
		$this->load_example_theme( 'default' );

		// Create a post.
		$post_id = self::factory()->post->create();

		// Create a term and add it to the post.
		$term_id = self::factory()->term->create( array(
			'taxonomy' => 'category',
			'name' => 'entertainment',
		) );
		wp_set_post_terms( $post_id, array( $term_id ), 'category' );

		// Create a taxonomy map.
		update_option( \Admin_Apple_Sections::TAXONOMY_MAPPING_KEY, array(
			'abcdef01-2345-6789-abcd-ef012356789a' => array( $term_id ),
		) );
		update_option( \Admin_Apple_Sections::THEME_MAPPING_KEY, array(
			'abcdef01-2345-6789-abcd-ef012356789a' => 'Colorful',
		) );

		// Cache as a transient to bypass the API call.
		$self = 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789a';
		set_transient(
			'apple_news_sections',
			array(
				(object) array(
					'createdAt' => '2017-01-01T00:00:00Z',
					'id' => 'abcdef01-2345-6789-abcd-ef012356789a',
					'isDefault' => true,
					'links' => (object) array(
						'channel' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890',
						'self' => $self,
					),
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name' => 'Main',
					'shareUrl' => 'https://apple.news/AbCdEfGhIj-KlMnOpQrStUv',
					'type' => 'section',
				),
			)
		);

		// Get sections for the post.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			$json['componentTextStyles']['dropcapBodyStyle']['textColor'],
			'#000000'
		);

		// Change the theme mapping to use the Default theme instead and re-test.
		update_option( \Admin_Apple_Sections::THEME_MAPPING_KEY, array(
			'abcdef01-2345-6789-abcd-ef012356789a' => 'Default',
		) );
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			$json['componentTextStyles']['dropcapBodyStyle']['textColor'],
			'#4f4f4f'
		);

		// Clean up.
		delete_option( \Admin_Apple_Sections::TAXONOMY_MAPPING_KEY );
		delete_option( \Admin_Apple_Sections::THEME_MAPPING_KEY );
		delete_transient( 'apple_news_sections' );
	}

	/**
	 * Tests the priority level setting. Ensures that a post that is mapped to
	 * multiple sections by taxonomy gets the theme that is associated with the
	 * section that has the highest priority among the sections assigned to the
	 * post.
	 */
	public function testPriority() {
		// Load an additional example theme to facilitate mapping.
		$this->load_example_theme( 'colorful' );

		// Ensure the default theme is active.
		$this->load_example_theme( 'default' );

		// Create a post.
		$post_id = self::factory()->post->create();

		// Create a term and add it to the post.
		$term_id = self::factory()->term->create( array(
			'taxonomy' => 'category',
			'name' => 'politics',
		) );
		wp_set_post_terms( $post_id, array( $term_id ), 'category' );

		// Create a taxonomy map that maps to multiple sections..
		update_option(
			\Admin_Apple_Sections::TAXONOMY_MAPPING_KEY,
			array(
				'abcdef01-2345-6789-abcd-ef012356789a' => array( $term_id ),
				'abcdef01-2345-6789-abcd-ef012356789b' => array( $term_id ),
			)
		);

		// Map each section to a different theme.
		update_option(
			\Admin_Apple_Sections::THEME_MAPPING_KEY,
			array(
				'abcdef01-2345-6789-abcd-ef012356789a' => 'Default',
				'abcdef01-2345-6789-abcd-ef012356789b' => 'Colorful',
			)
		);

		// Cache as a transient to bypass the API call.
		set_transient(
			'apple_news_sections',
			array(
				(object) array(
					'createdAt' => '2017-01-01T00:00:00Z',
					'id' => 'abcdef01-2345-6789-abcd-ef012356789a',
					'isDefault' => true,
					'links' => (object) array(
						'channel' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890',
						'self' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789a',
					),
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name' => 'Main',
					'shareUrl' => 'https://apple.news/AbCdEfGhIj-KlMnOpQrStUv',
					'type' => 'section',
				),
				(object) array(
					'createdAt' => '2017-01-01T00:00:00Z',
					'id' => 'abcdef01-2345-6789-abcd-ef012356789b',
					'isDefault' => false,
					'links' => (object) array(
						'channel' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef0123567890',
						'self' => 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789b',
					),
					'modifiedAt' => '2017-01-01T00:00:00Z',
					'name' => 'Secondary',
					'shareUrl' => 'https://apple.news/AbCdEfGhIj-KlMnOpQrStUw',
					'type' => 'section',
				),
			)
		);

		// Ensure that the default theme is used when no priority is specified.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			$json['componentTextStyles']['dropcapBodyStyle']['textColor'],
			'#4f4f4f'
		);

		// Set the priority on the sections to boost the priority of the secondary section.
		update_option(
			\Admin_Apple_Sections::PRIORITY_MAPPING_KEY,
			array(
				'abcdef01-2345-6789-abcd-ef012356789a' => 1,
				'abcdef01-2345-6789-abcd-ef012356789b' => 2,
			)
		);

		// Re-run the export and ensure the Colorful theme is used.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			$json['componentTextStyles']['dropcapBodyStyle']['textColor'],
			'#000000'
		);

		// Set the priority on the sections to boost the priority of the main section.
		update_option(
			\Admin_Apple_Sections::PRIORITY_MAPPING_KEY,
			array(
				'abcdef01-2345-6789-abcd-ef012356789a' => 2,
				'abcdef01-2345-6789-abcd-ef012356789b' => 1,
			)
		);

		// Re-run the export and ensure the Default theme is used.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			$json['componentTextStyles']['dropcapBodyStyle']['textColor'],
			'#4f4f4f'
		);

		// Clean up.
		delete_option( \Admin_Apple_Sections::PRIORITY_MAPPING_KEY );
		delete_option( \Admin_Apple_Sections::TAXONOMY_MAPPING_KEY );
		delete_option( \Admin_Apple_Sections::THEME_MAPPING_KEY );
		delete_transient( 'apple_news_sections' );
	}

	/**
	 * Tests the behavior of the apple_news_is_exporting() function.
	 *
	 * @access public
	 */
	public function testIsExporting() {

		// Setup.
		$title = 'My Title';
		$content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tristique quis justo sit amet eleifend. Praesent id metus semper, fermentum nibh at, malesuada enim. Mauris eget faucibus lectus. Vivamus iaculis eget urna non porttitor. Donec in dignissim neque. Vivamus ut ornare magna. Nulla eros nisi, maximus nec neque at, condimentum lobortis leo. Fusce in augue arcu. Curabitur lacus elit, venenatis a laoreet sit amet, imperdiet ac lorem. Curabitur sed leo sed ligula tempor feugiat. Cras in tellus et elit volutpat.</p>';
		$post_id = $this->factory->post->create( array(
			'post_title' => $title,
			'post_content' => $content,
		) );
		add_filter(
			'the_content',
			array( $this, 'filterTheContentTestIsExporting' )
		);

		// Ensure is_exporting returns false before exporting.
		$this->assertEquals(
			'is not exporting',
			apply_filters( 'the_content', 'Lorem ipsum dolor sit amet' )
		);

		// Get sections for the post.
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals(
			'<p>is exporting</p>',
			$json['components'][2]['text']
		);

		// Ensure is_exporting returns false after exporting.
		$this->assertEquals(
			'is not exporting',
			apply_filters( 'the_content', 'Lorem ipsum dolor sit amet' )
		);

		// Teardown.
		remove_filter(
			'the_content',
			array( $this, 'filterTheContentTestIsExporting' )
		);
	}
}
