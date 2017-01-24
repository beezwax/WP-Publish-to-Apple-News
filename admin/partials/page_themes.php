<div class="wrap apple-news-themes">
	<form method="post" action="" id="apple-news-themes-form">
		<?php wp_nonce_field( 'apple_news_themes', 'apple_news_themes' ); ?>
		<input type="hidden" name="action" value="apple_news_set_theme" />

		<?php submit_button(
			__( 'Create New Theme', 'apple-news' ),
			'secondary',
			'apple_news_create_theme',
			false
		 ); ?>
		 <?php submit_button(
			__( 'Upload Theme', 'apple-news' ),
			'secondary',
			'apple_news_upload_theme',
			false
		 ); ?>

		<?php submit_button(
			__( 'Set Theme', 'apple-news' ),
			'primary',
			'apple_news_set_theme'
		 ); ?>
	</form>
</div>
