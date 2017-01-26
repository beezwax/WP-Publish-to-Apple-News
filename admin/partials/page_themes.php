<div class="wrap apple-news-themes">
	<form method="post" action="" id="apple-news-themes-form">
		<?php wp_nonce_field( 'apple_news_themes', 'apple_news_themes' ); ?>
		<input type="hidden" name="action" value="apple_news_set_theme" />

		<?php submit_button(
			__( 'Create New Theme', 'apple-news' ),
			'secondary',
			'apple_news_start_create',
			false
		 ); ?>
		 <?php submit_button(
			__( 'Import Theme', 'apple-news' ),
			'secondary',
			'apple_news_start_import',
			false
		 ); ?>

		 <div class="apple-news-theme-form" id="apple_news_new_theme_options">
		 <b><?php esc_html_e( 'Theme name', 'apple-news' ) ?>:</b>
		 <input type="text" id="apple_news_theme_name" name="apple_news_theme_name" value="" maxlength="45" />
		 <?php submit_button(
			__( 'Save', 'apple-news' ),
			'primary',
			'apple_news_create_theme',
			false
		 ); ?>
		 <?php submit_button(
			__( 'Cancel', 'apple-news' ),
			'secondary',
			'apple_news_cancel_create_theme',
			false
		 ); ?>
		 </div>

		 <div class="apple-news-theme-form" id="apple_news_import_theme">
		 <p>
		 <b><?php esc_html_e( 'Choose a file to upload', 'apple-news' ) ?>:</b> <input type="file" id="apple_news_import_file" name="import" size="25" />
		 <br /><?php esc_html_e( '(max size 1MB)', 'apple-news' ) ?>
		 </p>
		 <?php submit_button(
			__( 'Upload', 'apple-news' ),
			'primary',
			'apple_news_upload_theme',
			false
		 ); ?>
		 <?php submit_button(
			__( 'Cancel', 'apple-news' ),
			'secondary',
			'apple_news_cancel_upload_theme',
			false
		 ); ?>
		 <input type="hidden" name="max_file_size" value="1000000" />
		 </div>

		<?php submit_button(
			__( 'Set Theme', 'apple-news' ),
			'primary',
			'apple_news_set_theme'
		 ); ?>
	</form>
</div>
