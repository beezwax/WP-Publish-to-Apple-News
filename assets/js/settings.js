(
	function ( $, window, undefined ) {
		'use strict';

    // storing RegExp strings for decoding the uploaded config file
    var RegExpStrings = {
      channel_id: /channel_id: ([0-9a-zA-Z_-]+)/,
      key: /key: ([0-9a-zA-Z_-]+)/,
      secret: /secret: ([0-9a-zA-Z_-]+)/
    }

    function updateCreds(input) {
      var channelIdMatch = input.match(RegExpStrings.channel_id).toString().split(" ");
      $( '#api_channel' ).val(channelIdMatch ? channelIdMatch[1] : '');

      var keyMatch = input.match(RegExpStrings.key).toString().split(" ");
      $( '#api_key' ).val(keyMatch ? keyMatch[1] : '');

      var secretMatch = input.match(RegExpStrings.secret).toString().split(" ");
      $( '#api_secret' ).val(secretMatch ? secretMatch[1] : '');
    }

    // Hide manual-input textarea for creds on load.
    $( '#api_config_file_input' ).css({
        'display': 'none',
        'width': '300px',
        'height': '250px'
    });

    $( 'a[href$="#api_config_file"]' ).click(function () {
      $( '#api_config_file_input' ).css({
              'display': 'block'
      });
    });


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
          var contents = f.target.result;
          updateCreds(contents);
          $( '#api_config_file_input' ).val(contents);
        };
        reader.readAsText(file);
      }
		} );

    // Reading in the needed values from the hidden textarea field.
    $( '#api_config_file_input' ).on( 'change', function () {
      var input = $( '#api_config_file_input' ).val();
      updateCreds(input);
    });
	}
)( jQuery, window );
