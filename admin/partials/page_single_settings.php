<div class="wrap">
	<h1>&ldquo;<?php echo $post->post_title; ?>&rdquo; Options</h1>

	<?php if ( isset( $message ) ): ?>
	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p><strong><?php echo $message ?></strong></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'apple-news' ) ?></span></button>
	</div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'export', 'apple-export-nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">Pull quote</th>
				<td>
				<textarea name="pullquote" placeholder="Lorem ipsum..." rows="10" class="large-text"><?php echo @$post_meta[ 'apple_export_pullquote' ][0] ?></textarea>
					<p class="description"><?php esc_html_e( 'This is optional and can be left blank. A pull quote is a key phrase, quotation, or excerpt that has been pulled from an article and used as a graphic element, serving to entice readers into the article or to highlight a key topic.', 'apple-news' ) ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Pull quote position', 'apple-news' ) ?></th>
				<td>
					<select name="pullquote_position">
						<option <?php if ( 'top' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="top"><?php esc_html_e( 'top', 'apple-news' ) ?></option>
						<option <?php if ( 'middle' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="middle"><?php esc_html_e( 'middle', 'apple-news' ) ?></option>
						<option <?php if ( 'bottom' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="bottom"><?php esc_html_e( 'bottom', 'apple-news' ) ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'The position in the article the pull quote will appear.', 'apple-news' ) ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<a href="<?php echo admin_url( 'admin.php?page=apple_export_index' ); ?>" class="button"><?php esc_html_e( 'Back', 'apple-news' ) ?></a>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'apple-news' ) ?></button>
		</p>
	</form>
</div>
