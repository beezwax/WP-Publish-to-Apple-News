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

	// Load mocks for integration tests.
	require_once __DIR__ . '/mocks/class-bc-setup.php';
	if ( ! function_exists( 'coauthors' ) ) {
		require_once __DIR__ . '/mocks/function-coauthors.php';
	}

	// Activate mocked Brightcove functionality.
	$bc_setup = new BC_Setup();
	$bc_setup->action_init();

	// Load the plugin.
	require dirname( dirname( __FILE__ ) ) . '/apple-news.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Disable CAP by default - make it opt-in in tests.
tests_add_filter( 'apple_news_use_coauthors', '__return_false' );

// Filter the list of allowed protocols to allow Apple News-specific ones.
tests_add_filter(
	'kses_allowed_protocols',
	function ( $protocols ) {
		return array_merge(
			(array) $protocols,
			[
				'music',
				'musics',
				'stocks',
			]
		);
	}
);

require $_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/class-apple-news-testcase.php';

require_once __DIR__ . '/apple-exporter/components/class-component-testcase.php';
