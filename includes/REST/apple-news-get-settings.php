<?php
/**
 * This adds custom endpoints for perspective posts.
 *
 * @package Apple_News
 */
namespace Apple_News\REST;
/**
 * Get API response.
 *
 * @param array $data data from query args.
 * @return array updated response.
 */
function get_settings_response( $data ) {
  $response = [];

	if ( ! empty( get_current_user_id() ) ) {
		// Get admin settings
		$admin_settings = new \Admin_Apple_Settings();
		$settings = $admin_settings->fetch_settings();
		$response['enableCoverArt'] = 'no' !== $settings->enable_cover_art;
		$response['adminUrl'] = esc_url( admin_url( 'admin.php?page=apple-news-options' ) );
	}

  return $response;
}

/**
 * Initialize this REST Endpoint.
 */
add_action(
	'rest_api_init',
	function () {
		// Register route count argument.
		register_rest_route(
			'apple-news/v1',
			'/get-settings',
			[
				'methods'  => 'GET',
				'callback' => __NAMESPACE__ . '\get_settings_response',
			]
		);
	}
);
