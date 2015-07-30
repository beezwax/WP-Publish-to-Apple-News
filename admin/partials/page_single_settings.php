<div class="wrap">
	<h1>&ldquo;<?php echo $post->post_title; ?>&rdquo; Options</h1>

	<?php if ( isset( $message ) ): ?>
	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p><strong><?php echo $message ?></strong></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'export', 'apple-export-nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">Pull quote</th>
				<td>
				<textarea name="pullquote" placeholder="Lorem ipsum..." rows="10" class="large-text"><?php echo @$post_meta[ 'apple_export_pullquote' ][0] ?></textarea>
					<p class="description">This is optional and can be left blank. A pull
					quote is a key phrase, quotation, or excerpt that has been pulled from an
					article and used as a graphic element, serving to entice readers into the
					article or to highlight a key topic.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">Pull quote position</th>
				<td>
					<select name="pullquote_position">
						<option <?php if ( 'top' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="top">top</option>
						<option <?php if ( 'middle' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="middle">middle</option>
						<option <?php if ( 'bottom' == @$post_meta['apple_export_pullquote_position'][0] ) echo 'selected' ?> value="bottom">bottom</option>
					</select>
					<p class="description">The position in the article the pullquote will appear.</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<a href="<?php echo admin_url( 'admin.php?page=apple_export_index' ); ?>" class="button">Back</a>
			<button type="submit" class="button button-primary">Save Changes</button>
		</p>
	</form>
</div>
