<div class="wrap">
	<h1>Apple News Options</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'apple-export-options' ); ?>
		<?php do_settings_sections( 'apple-export-options' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
