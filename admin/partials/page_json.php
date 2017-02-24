<?php $themes = new \Admin_Apple_Themes(); ?>
<div class="wrap apple-news-json">
	<h1 id="apple_news_json_title"><?php esc_html_e( 'Customize Component JSON', 'apple-news' ) ?></h1>

	<form method="post" action="" id="apple-news-json-form">
		<?php wp_nonce_field( 'apple_news_json' ); ?>
		<input type="hidden" id="apple_news_action" name="action" value="apple_news_set_json" />

		<?php submit_button(
			__( 'Save JSON', 'apple-news' ),
			'primary',
			'apple_news_save_json'
		); ?>
	</form>
</div>
