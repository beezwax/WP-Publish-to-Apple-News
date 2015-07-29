<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( 'apple-export-options' ); ?>

		<?php foreach ( $sections as $section ): ?>
		<h3><?php _e( $section->name() ); ?></h3>
		<?php $section->print_section_info(); ?>
		<table class="form-table apple-export">
			<?php foreach ( $section->groups() as $group ): ?>
			<tr>
				<th scope="row"><?php _e( $group['label'] ); ?></th>
				<td>
					<fieldset>
						<?php foreach ( $group['settings'] as $setting_name => $setting_meta ): ?>
						<label class="setting-container">
							<?php if ( !empty( $setting_meta['label'] ) ): ?>
								<span class="label-name"><?php echo $setting_meta['label']; ?></span>
							<?php endif; ?>
							<?php $section->render_field( array( $setting_name, $setting_meta['default'] ) ); ?>
						</label>
						<br />
						<?php endforeach; ?>

						<?php if ( $group['description'] ): ?>
							<p class="description"><?php echo '(' . __( $group['description'] ) . ')'; ?></p>
						<?php endif; ?>
					</fieldset>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endforeach; ?>

		<?php submit_button(); ?>
	</form>
</div>
