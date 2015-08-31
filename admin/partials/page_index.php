<?php $current_screen = get_current_screen(); ?>
<div class="wrap">
	<h1><?php esc_html_e( 'Apple News', 'apple-news' ) ?></h1>

	<?php Admin_Apple_Notice::show(); ?>

	<?php if ( isset( $message ) ): ?>
	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p><strong><?php echo esc_html( $message ) ?></strong></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice', 'apple-news' ) ?>.</span></button>
	</div>
	<?php endif; ?>

	<form method="get">
		<?php if ( ! empty( $current_screen->parent_base ) ): ?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $current_screen->parent_base ) ?>">
		<?php endif; ?>
		<?php $table->display(); ?>
	</form>
</div>
