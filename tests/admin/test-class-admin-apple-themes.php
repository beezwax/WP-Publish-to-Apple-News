<?php

use \Apple_Exporter\Settings as Settings;

class Admin_Apple_Themes_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();

		$this->settings = new Settings();
	}

	public function testCreateTheme() {
		// Set the POST data required to create a new theme
		$name = 'Test Theme';
		$_POST['apple_news_theme_name'] = $name;
		$_POST['action'] = 'apple_news_create_theme';
		$_POST['_wp_http_referer'] = '/wp-admin/admin.php?page=apple-news-themes';
		$_POST['apple_news_themes'] = wp_create_nonce( 'apple_news_themes' );

		$themes = new \Admin_Apple_Themes();
		$themes->action_router();

		// Check that the data was saved properly
		$current_settings = $this->settings->all();

		// Get only formatting settings
		$formatting = new Admin_Apple_Settings_Section_Formatting( '' );
		$formatting_settings = $formatting->get_settings();

		$formatting_settings_keys = array_keys( $formatting_settings );
		$diff_settings = array();

		foreach ( $formatting_settings_keys as $key ) {
			if ( isset( $current_settings[ $key ] ) ) {
				$diff_settings[ $key ] = $current_settings[ $key ];
			}
		}

		// Array diff against the option value
		$new_theme_settings = get_option( $themes->theme_key_from_name( $name ) );

		$this->assertEquals( $diff_settings, $new_theme_settings );
	}
}

