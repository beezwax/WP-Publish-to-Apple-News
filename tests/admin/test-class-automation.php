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
class Automation_Test extends Apple_News_Testcase {
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
				'abcdef01-2345-6789-abcd-ef0123567890' => [1],
				'bcdef012-3456-789a-bcde-f01235678901' => [2],
				'cdef0123-4567-89ab-cdef-012356789012' => [3, 4],
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
					'field'    => 'links.sections',
					'taxonomy' => 'category',
					'term_id'  => 4,
					'value'    => 'cdef0123-4567-89ab-cdef-012356789012',
				],
				[
					'field'    => 'theme',
					'taxonomy' => 'category',
					'term_id'  => 3,
					'value'    => 'Entertainment Theme',
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
}
