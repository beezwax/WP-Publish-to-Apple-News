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
          // When a file is uploaded, the read contents populate the hidden textarea.
          $( '#api_config_file_input' ).val(f.target.result);
        };
        reader.readAsText(file);
      }
		} );

    // Reading in the needed values from the hidden textarea field.
    $( '#api_config_file_input' ).on( 'change', function () {
      var input = $( '#api_config_file_input' ).val();

      var RegExpStrings = {
        channel_id: /channel_id: [0-9a-zA-Z_-]*/g,
        key: /key: [0-9a-zA-Z_-]*/g,
        secret: /secret: [0-9a-zA-Z_-]*/g
      }

      $( '#api_channel' ).val(
        input.match(RegExpStrings.channel_id).toString().split(" ")[1]
      );
      $( '#api_key' ).val(
        input.match(RegExpStrings.key).toString().split(" ")[1]
      );
      $( '#api_secret' ).val(
        input.match(RegExpStrings.secret).toString().split(" ")[1]
      );
    });
	}
)( jQuery, window );
