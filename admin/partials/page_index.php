<div class="wrap">
	<h1>Apple News</h1>

	<?php if ( isset( $_SESSION['apple_export_flash'] ) ): ?>
		<div class="flash-message">
			<h3>Oops! Something went wrong</h3>
			<?php echo $_SESSION['apple_export_flash'] ?>
		</div>
		<?php unset( $_SESSION['apple_export_flash'] ); ?>
	<?php endif; ?>

	<?php if ( isset( $message ) ): ?>
	<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
		<p><strong><?php echo $message ?></strong></p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
	<?php endif; ?>

	<form method="get">
		<input type="hidden" name="page" value="<?php echo htmlentities( $_REQUEST['page'] ) ?>">
		<?php $table->display(); ?>
	</form>
</div>
