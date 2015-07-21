<?php
/**
 * This class manages flash messages.
 *
 * @since 0.6.0
 */
class Flash {

	const PREFIX = 'apple_export_flash_';

	function __construct() {
		session_start();
		add_action( 'admin_head', array( $this, 'register_styles' ) );
	}

	public function register_styles() {
		echo '<style type="text/css">';
		echo '.apple-export.flash-message { margin: 2em 0; border-radius: 2px; padding: 0.5em 1em; border: 1px solid #bce8f1; background-color: #d9edf7; color: #31708f; }';
		echo '.apple-export.flash-message h3 { margin: 0.25em 0 0.5em; padding: 0; }';
		echo '.apple-export.flash-message.success { border-color: #d6e9c6; background-color: #dff0d8; color: #3c763d; }';
		echo '.apple-export.flash-message.error { border-color: #ebccd1; background-color: #f2dede; color: #a94442; }';
		echo '</style>';
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
		<div class="apple-export flash-message error">
			<h3>Oops! Something went wrong</h3>
			<?php echo $message; ?>
		</div>
		<?php
	}

	private static function show_success_flash( $message ) {
		?>
		<div class="apple-export flash-message success">
			<h3>Success</h3>
			<?php echo $message; ?>
		</div>
		<?php
	}

	private static function show_info_flash( $message ) {
		?>
		<div class="apple-export flash-message info">
			<?php echo $message; ?>
		</div>
		<?php
	}

}
