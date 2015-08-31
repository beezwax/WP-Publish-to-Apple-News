<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( 'apple-export-options' ); ?>

		<?php foreach ( $sections as $section ): ?>
		<h3><?php echo esc_html( $section->name() ); ?></h3>
		<?php echo wp_kses_post( $section->get_section_info() ); ?>
		<table class="form-table apple-export">
			<?php foreach ( $section->groups() as $group ): ?>
			<tr>
				<th scope="row"><?php echo esc_html( $group['label'] ); ?></th>
				<td>
					<fieldset>
						<?php foreach ( $group['settings'] as $setting_name => $setting_meta ): ?>
						<label class="setting-container">
							<?php if ( ! empty( $setting_meta['label'] ) ): ?>
								<span class="label-name"><?php echo esc_html( $setting_meta['label'] ); ?></span>
							<?php endif; ?>
							<?php echo wp_kses( $section->render_field( array( $setting_name, $setting_meta['default'] ) ), Admin_Apple_Settings_Section::ALLOWED_HTML ); ?>
						</label>
						<br />
						<?php endforeach; ?>

						<?php if ( $group['description'] ): ?>
							<p class="description"><?php echo '(' . wp_kses_post( $group['description'] ) . ')'; ?></p>
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
