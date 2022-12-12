<?php
/**
 * Publish to Apple News partials: Notice template
 *
 * phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
 *
 * @global string $message
 * @global string $type
 *
 * @package Apple_News
 */

$apple_allowed_html = [
	'a'  => [ 'href' => [] ],
	'br' => [],
]
?>
<div
	class="notice <?php echo sanitize_html_class( 'notice-' . $type ); ?> apple-news-notice is-dismissible"
	data-message="<?php echo esc_attr( $message ); ?>"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'apple_news_dismiss_notice' ) ); ?>"
	data-type="<?php echo esc_attr( $type ); ?>"
>
	<p><strong><?php echo wp_kses( $message, $apple_allowed_html ); ?></strong></p>
</div>
