<div class="wrap">
	<h1>Apple Export Options</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'apple-export-options' ); ?>
		<?php do_settings_sections( 'apple-export-options' ); ?>

		<!--
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Body Font</th>
				<td>
					<input type="text" name="body_font" value="<?php echo esc_attr( get_option( 'body_font' ) ); ?>">
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Body Size</th>
				<td>
					<input type="text" name="body_size" value="<?php echo esc_attr( get_option( 'body_size' ) ); ?>">
				</td>
			</tr>
		</table>
		-->

		<?php submit_button(); ?>
	</form>
</div>
