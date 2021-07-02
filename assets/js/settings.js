(
	function ( $, window, undefined ) {
		'use strict';

		// Listen for changes to the debugging settings.
		$( '#apple_news_enable_debugging' ).on( 'change', function () {
			var $email = $( '#apple_news_admin_email' );
			if ( 'yes' === $( this ).val() ) {
				$email.attr( 'required', 'required' );
			} else {
				$email.removeAttr( 'required' );
			}
		} ).change();

    // Code for reading uploaded papi file.
		$( '#api_config_file' ).on( 'change', function (e) {
			if (!e.target.files || !e.target.files[0]) {
        return;
      } else {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = function(f) {
          $( '#api_config_file_input' ).text(f.target.result);
        };
        reader.readAsText(file);
      }
		} );
	}
)( jQuery, window );
