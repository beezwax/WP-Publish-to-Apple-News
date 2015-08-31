<?php
/**
 * This class manages flash messages.
 *
 * @since 0.6.0
 */
class Flash {

	/**
	 * Prepare items for the table.
	 *
	 * @access public
	 */
	const PREFIX = 'apple_export_flash_';

	function __construct() {
		@session_start();
	}

	public static function message( $message, $type ) {
		$_SESSION[ self::PREFIX . 'message' ] = $message;
		$_SESSION[ self::PREFIX . 'type' ]    = $type;
	}

	public static function info( $message ) {
		self::message( $message, 'info' );
	}

	public static function success( $message ) {
		self::message( $message, 'success' );
	}

	public static function error( $message ) {
		self::message( $message, 'error' );
	}

	public static function has_flash() {
		return isset( $_SESSION[ self::PREFIX . 'message' ] );
	}

	public static function show() {
		if ( ! self::has_flash() ) {
			return;
		}

		$message = $_SESSION[ self::PREFIX . 'message' ];
		$type    = $_SESSION[ self::PREFIX . 'type' ];

		switch ( $type ) {
		case 'success':
			self::show_success_flash( $message );
			break;
		case 'error':
			self::show_error_flash( $message );
			break;
		case 'info':
		default:
			self::show_info_flash( $message );
		}

		unset( $_SESSION[ self::PREFIX . 'message' ] );
		unset( $_SESSION[ self::PREFIX . 'type' ] );
	}

	private static function show_error_flash( $message ) {
		?>
		<div class="notice error is-dismissible">
			<p><strong><?php echo wp_kses_post( $message ) ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'apple-news' ) ?></span></button>
		</div>
		<?php
	}

	private static function show_success_flash( $message ) {
		?>
		<div class="notice updated is-dismissible">
			<p><strong><?php echo wp_kses_post( $message ) ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'apple-news' ) ?></span></button>
		</div>
		<?php
	}

	private static function show_info_flash( $message ) {
		?>
		<div class="notice is-dismissible">
			<p><strong><?php echo wp_kses_post( $message ) ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'apple-news' ) ?></span></button>
		</div>
		<?php
	}

}
