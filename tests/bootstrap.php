<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

// Autoloading for prophecy
require_once dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';

/**
 * Manually load the plugin for tests.
 */
function _manually_load_plugin() {
	// Disable VIP cache manager when testing against VIP Go integration.
	if ( method_exists( 'WPCOM_VIP_Cache_Manager', 'instance' ) ) {
		remove_action( 'init', [ WPCOM_VIP_Cache_Manager::instance(), 'init' ] );
	}

	// Set the permalink structure.
	update_option( 'permalink_structure', '/%postname%' );

	// Load the plugin.
	require dirname( dirname( __FILE__ ) ) . '/apple-news.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Turn off Gutenberg for tests, at least for now.
tests_add_filter( 'apple_news_block_editor_is_active', '__return_false' );

require $_tests_dir . '/includes/bootstrap.php';
