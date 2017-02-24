<?php $themes = new \Admin_Apple_Themes(); ?>
<div class="wrap apple-news-json">
	<h1 id="apple_news_json_title"><?php esc_html_e( 'Customize Component JSON', 'apple-news' ) ?></h1>

	<form method="post" action="" id="apple-news-json-form">
		<?php wp_nonce_field( 'apple_news_json' ); ?>
		<input type="hidden" id="apple_news_action" name="action" value="apple_news_save_json" />

		<?php if ( empty( $components ) ) : ?>
		<h2><?php esc_html_e( 'No components are available for customizing JSON', 'apple-news' ) ?></h2>
		<?php else : ?>
			<p><?php echo wp_kses(
				sprintf(
					__( 'Select a component to customize any of the specs for its JSON snippets. This will enable to you create advanced templates beyond what is supported by <a href="%s">themes</a>.', 'apple-news' ),
					esc_url( $theme_admin_url )
				),
				array(
					'a' => array(
						'href' => array()
					)
				)
			) ?></p>
			<p><?php esc_html_e( 'Tokens that will be replaced by dynamic values based on theme or post settings are denoted as %%token%%. You may remove tokens to suit your custom JSON but you cannot add new ones.', 'apple-news' ) ?></p>
			<p><?php echo wp_kses(
				sprintf(
					__( 'For more information on the Apple News format options for each component, please read the <a href="%s">Apple News Format Reference</a>.', 'apple-news' ),
					'https://developer.apple.com/library/content/documentation/General/Conceptual/Apple_News_Format_Ref/Component.html#//apple_ref/doc/uid/TP40015408-CH5-SW1'
				),
				array(
					'a' => array(
						'href' => array()
					)
				)
			) ?></p>
			<select id="apple_news_component" name="apple_news_component">
				<option name=""><?php esc_html_e( 'Select a component', 'apple-news' ) ?></option>
				<?php foreach ( $components as $shortname => $name ) : ?>
					<option name="<?php echo esc_attr( $shortname ) ?>"><?php echo esc_html( $name ) ?></option>
				<?php endforeach; ?>
			</select>

		<?php endif; ?>
		<?php submit_button(
			__( 'Save JSON', 'apple-news' ),
			'primary',
			'apple_news_save_json'
		); ?>
	</form>
</div>
