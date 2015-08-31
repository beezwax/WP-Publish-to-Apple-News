<?php $current_screen = get_current_screen(); ?>
<div class="wrap">
	<h1><?php esc_html_e( 'Apple News', 'apple-news' ) ?></h1>

	<form method="get">
		<?php if ( ! empty( $current_screen->parent_base ) ): ?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $current_screen->parent_base ) ?>">
		<?php endif; ?>
		<?php $table->display(); ?>
	</form>
</div>
