<div class="wrap apple-news-theme-edit">
	<form method="post" action="" id="apple-news-theme-edit-form">
		<?php wp_nonce_field( 'apple_news_save_theme' ); ?>
		<input type="hidden" name="action" value="apple_news_save_theme" />
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
