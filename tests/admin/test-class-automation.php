<?php
/**
 * Publish to Apple News Tests: Automation_Test class
 *
 * Contains a class which is used to test Apple_News\Admin\Automation.
 *
 * @package Apple_News
 * @since 2.4.0
 */

use Apple_News\Admin\Automation;

/**
 * A class which is used to test the Apple_News\Admin\Automation class.
 */
class Apple_News_Automation_Test extends Apple_News_Testcase {
	/**
	 * Returns an array of arrays representing function arguments to the
	 * test_metadata_automation function.
	 */
	public function data_metadata_automation() {
		return [
			[ 'isHidden' ],
			[ 'isPaid' ],
			[ 'isPreview' ],
			[ 'isSponsored' ],
		];
	}

	/**
	 * Ensures that named metadata is properly set via an automation process.
	 *
	 * @dataProvider data_metadata_automation
	 *
	 * @param string $flag The flag that should be set by automation.
	 */
	public function test_metadata_automation( $flag ) {
		$post_id = self::factory()->post->create();

		// Create an automation routine for this flag.
		$result  = wp_insert_term( 'Test Flag ' . $flag, 'category' );
		$term_id = $result['term_id'];
		update_option(
			'apple_news_automation',
			[
				[
					'field'    => $flag,
					'taxonomy' => 'category',
					'term_id'  => $term_id,
					'value'    => 'true',
				],
			]
		);

		// Set the taxonomy term to trigger the automation routine and ensure the flag is set.
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );
		$this->assertEquals( true, $metadata['data'][ $flag ] );
	}

	/**
	 * Tests ability to automate setting a section.
	 */
	public function test_sections_automation() {
		$post_id = self::factory()->post->create();

		// Create an automation routine for section mapping.
		$result  = wp_insert_term( 'Test Section Category', 'category' );
		$term_id = $result['term_id'];
		update_option(
			'apple_news_automation',
			[
				[
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => $term_id,
					'value'    => 'abcdef01-2345-6789-abcd-ef012356789b',
				],
			]
		);

		// Set the taxonomy term to trigger the automation routine and ensure the flag is set.
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );
		$request  = $this->get_request_for_post( $post_id );
		$metadata = $this->get_metadata_from_request( $request );
		$this->assertEquals(
			[ 'https://news-api.apple.com/channels/abcdef01-2345-6789-abcd-ef012356789b' ],
			$metadata['data']['links']['sections']
		);
	}

	/**
	 * Tests settings migration from the old Sections paradigm to Automation.
	 */
	public function test_settings_migration() {
		// Set up the legacy options.
		update_option(
			'apple_news_section_priority_mappings',
			[
				'abcdef01-2345-6789-abcd-ef0123567890' => 1,
				'bcdef012-3456-789a-bcde-f01235678901' => 2,
				'cdef0123-4567-89ab-cdef-012356789012' => 3,
			]
		);
		update_option(
			'apple_news_section_taxonomy_mappings',
			[
				'abcdef01-2345-6789-abcd-ef0123567890' => [ 1 ],
				'bcdef012-3456-789a-bcde-f01235678901' => [ 2 ],
				'cdef0123-4567-89ab-cdef-012356789012' => [ 3, 4 ],
			]
		);
		update_option(
			'apple_news_section_theme_mappings',
			[
				'abcdef01-2345-6789-abcd-ef0123567890' => 'Primary Theme',
				'bcdef012-3456-789a-bcde-f01235678901' => 'News Theme',
				'cdef0123-4567-89ab-cdef-012356789012' => 'Entertainment Theme',
			]
		);

		// Trigger the migration to automation settings and confirm the result.
		$apple_news = new Apple_News();
		$apple_news->upgrade_to_2_4_0();
		$this->assertEquals(
			[
				[
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => 3,
					'value'    => 'cdef0123-4567-89ab-cdef-012356789012',
				],
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => 3,
					'value'    => 'Entertainment Theme',
				],
				[
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => 4,
					'value'    => 'cdef0123-4567-89ab-cdef-012356789012',
				],
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => 4,
					'value'    => 'Entertainment Theme',
				],
				[
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => 2,
					'value'    => 'bcdef012-3456-789a-bcde-f01235678901',
				],
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => 2,
					'value'    => 'News Theme',
				],
				[
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => 1,
					'value'    => 'abcdef01-2345-6789-abcd-ef0123567890',
				],
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => 1,
					'value'    => 'Primary Theme',
				],
			],
			Automation::get_automation_rules()
		);
		$this->assertFalse( get_option( 'apple_news_section_priority_mappings' ) );
		$this->assertFalse( get_option( 'apple_news_section_taxonomy_mappings' ) );
		$this->assertFalse( get_option( 'apple_news_section_theme_mappings' ) );
	}

	/**
	 * Tests automation of theme selection by taxonomy.
	 */
	public function test_theme_automation() {
		// Load some themes so we have more than one to choose from.
		$this->load_example_theme( 'colorful' );
		$this->load_example_theme( 'default' );

		$post_id = self::factory()->post->create();

		// Create an automation routine for selecting the theme based on category.
		$result  = wp_insert_term( 'Entertainment', 'category' );
		$term_id = $result['term_id'];
		update_option(
			'apple_news_automation',
			[
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => $term_id,
					'value'    => 'Colorful',
				],
			]
		);

		// Set the taxonomy term to trigger the automation routine and ensure the correct theme is chosen.
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );
		$json = $this->get_json_for_post( $post_id );
		$this->assertEquals( '#000000', $json['componentTextStyles']['dropcapBodyStyle']['textColor'] );
	}
}
