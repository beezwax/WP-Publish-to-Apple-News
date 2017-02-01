<div class="wrap apple-news-theme-edit">
	<form method="post" action="" id="apple-news-theme-edit-form">
		<?php wp_nonce_field( 'apple_news_save_edit_theme' ); ?>
		<input type="hidden" name="action" value="apple_news_save_edit_theme" />
		<p>
			<label id="apple_news_theme_name_label" for="apple_news_theme_name"><?php echo esc_html_e( 'Theme Name', 'apple-news' ) ?></label>
			<br />
			<input type="text" id="apple_news_theme_name" name="apple_news_theme_name" value="<?php echo esc_attr( $theme_name ) ?>" maxlength="45" />
			<input type="hidden" id="apple_news_theme_name_previous" name="apple_news_theme_name_previous" value="<?php echo esc_attr( $theme_name ) ?>" />
		</p>
		<?php
			// Get formatting settings
			$section = new Admin_Apple_Settings_Section_Formatting( 'apple-news-theme-edit' );
			$section->before_section();
			include plugin_dir_path( __FILE__ ) . 'page_options_section.php';
			$section->after_section();
			submit_button();
		?>
	</form>
</div>
