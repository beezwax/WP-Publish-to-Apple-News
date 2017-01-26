(function ($) {

	$(document).ready(function () {
		$( '#apple_news_start_create' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#apple_news_new_theme_options' ).toggle();
		});

		$( '#apple_news_cancel_create_theme' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#apple_news_theme_name' ).val( '' );
			$( '#apple_news_new_theme_options' ).toggle();
			$( '#apple_news_theme_error' ).remove();
		});

		$( '#apple_news_create_theme' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#apple_news_theme_error' ).remove();
			if ( 0 === $( '#apple_news_theme_name' ).val().length ) {
				appleNewsThemesShowError( '#apple_news_new_theme_options', appleNewsThemes.noNameError );
			}

			if ( 45 < $( '#apple_news_theme_name' ).val().length ) {
				appleNewsThemesShowError( '#apple_news_new_theme_options', appleNewsThemes.tooLongError );
			}
		});

		$( '#apple_news_start_import' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#apple_news_import_theme' ).toggle();
		});

		$( '#apple_news_cancel_upload_theme' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '#apple_news_import_theme' ).toggle();
		});

		// TODO
		// Don't allow deleting the active theme
		// Need actions for delete and export
	});

	function appleNewsThemesShowError( selector, message ) {
		$( selector ).append( $( '<p>' )
			.attr( 'id', 'apple_news_theme_error' )
			.addClass( 'error' )
			.text( message )
		);
	}

}( jQuery ) );
