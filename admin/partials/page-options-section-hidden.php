<?php
/**
 * Publish to Apple News partials: Options Section Hidden page template
 *
 * phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
 *
 * @global Admin_Apple_Settings_Section $apple_section
 *
 * @package Apple_News
 */

foreach ( $apple_section->groups() as $apple_group ) {
	/** This action is documented in admin/partials/page-theme-edit.php */
	do_action( 'apple_news_before_setting_group', $apple_group, true );
	foreach ( $apple_group['settings'] as $apple_setting_name => $apple_setting_meta ) {
		/** This action is documented in admin/partials/page-theme-edit.php */
		do_action( 'apple_news_before_setting', $apple_setting_name, $apple_setting_meta );
		echo wp_kses(
			$apple_section->render_field(
				[
					$apple_setting_name,
					$apple_setting_meta['default'],
					$apple_setting_meta['callback'],
				]
			),
			Admin_Apple_Settings_Section::$allowed_html
		);
		/** This action is documented in admin/partials/page-theme-edit.php */
		do_action( 'apple_news_after_setting', $apple_setting_name, $apple_setting_meta );
	}
	/** This action is documented in admin/partials/page-theme-edit.php */
	do_action( 'apple_news_after_setting_group', $apple_group, true );
}
