<?php
/**
 * Publish to Apple News partials: Automation page template
 *
 * @package Apple_News
 */

?>
<div class="wrap apple-news-settings">
	<h1><?php esc_html_e( 'Apple News Automation', 'apple-news' ); ?></h1>
	<form method="post" action="" id="apple-news-automation-form">
		<?php wp_nonce_field( 'apple-news-automation' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
