(function ($) {

	$(document).ready(function () {
		appleNewsSelectInit();
		appleNewsSettingsSortInit( '#meta-component-order-sort', 'meta_component_order' );
		appleNewsColorPickerInit();
		appleNewsPreviewInit();
	});

	function appleNewsFontSelectTemplate( font ) {
		var $fontOption = $( '<span>' )
			.attr( 'style', 'font-family: ' + font.text )
			.text( font.text );

		return $fontOption;
	}

	function appleNewsSelectInit() {
		// Only show fonts on Macs since they're system fonts
		if ( appleNewsSupportsMacFeatures() ) {
			$( '.select2.standard' ).select2();
			$( '.select2.font' ).select2({
				templateResult: appleNewsFontSelectTemplate,
				templateSelection: appleNewsFontSelectTemplate
			});
		} else {
			$( '.select2' ).select2();
			$( 'span.select2' ).after(
				$( '<div>' )
					.addClass( 'font-notice' )
					.text( appleNewsSettings.fontNotice )
			)
		}
	}

	function appleNewsSettingsSortInit( selector, key ) {
		$( selector ).sortable( {
			'stop' : function( event, ui ) {
				appleNewsSettingsSortUpdate( $( this ), key );
			},
		} );
   	$( selector ).disableSelection();
   	appleNewsSettingsSortUpdate( $( selector ), key );
	}

	function appleNewsSettingsSortUpdate( $sortableElement, keyPrefix ) {
		// Build the key for field
		var key = keyPrefix + '[]';

		// Remove any current values
		$( 'input[name="' + key + '"]' ).remove();

		// Create a hidden form field with the values of the sortable element
		var values = $sortableElement.sortable( 'toArray' );
		if ( values.length > 0 ) {
			$.each( values.reverse(), function( index, value ) {
				$hidden = $( '<input>' )
					.attr( 'type', 'hidden' )
					.attr( 'name', key )
					.attr( 'value', value );

				$sortableElement.after( $hidden );
			} );
		}

		// Update the preview
		appleNewsUpdatePreview();
	}

	function appleNewsColorPickerInit() {
		$( '.apple-news-color-picker' ).iris({
			palettes: true,
			width: 320,
			change: appleNewsUpdatePreview
		});

		$( '.apple-news-color-picker' ).on( 'click', function() {
			$( '.apple-news-color-picker' ).iris( 'hide' );
			$( this ).iris( 'show' );
		});
	}

	function appleNewsSupportsMacFeatures() {
		if ( 'MacIntel' === navigator.platform ) {
			return true;
		} else {
			return false;
		}
	}

	function appleNewsPreviewInit() {
		// Do an initial update
		appleNewsUpdatePreview();

		// Ensure all further updates also affect the preview
		$( '#apple-news-settings-form :input' ).on( 'change', appleNewsUpdatePreview );

		// Show the preview
		$( '.apple-news-settings-preview' ).show();
	}

	function appleNewsUpdatePreview() {
		console.log( 'updating preview' );

		// Create a map of the form values to the preview elements
		appleNewsSetCSS( '.apple-news-settings-preview', 'layout_margin', 'padding-left', 'pt', .1 );
		appleNewsSetCSS( '.apple-news-settings-preview', 'layout_margin', 'padding-right', 'pt', .1 );
		appleNewsSetCSS( '.apple-news-settings-preview p', 'body_font', 'font-family', null, null );
		appleNewsSetCSS( '.apple-news-settings-preview p', 'body_size', 'font-size', 'pt', null );
		appleNewsSetCSS( '.apple-news-settings-preview p', 'body_color', 'color', null, null );
		appleNewsSetCSS( '.apple-news-settings-preview a', 'body_link_color', 'color', null, null );
		appleNewsSetCSS( '.apple-news-settings-preview', 'body_background_color', 'background-color', null, null );
		appleNewsSetCSS( '.apple-news-settings-preview p', 'body_orientation', 'text-align', null, null );
	}

	function appleNewsSetCSS( displayElement, formElement, property, units, scale ) {
		// Get the form value
		console.log( formElement );
		var value = $( '#' + formElement ).val();

		// Add units if set and we got a value
		if ( units && value ) {
			value = value + units;
		}

		// Some values need to be scaled
		if ( scale ) {
			value = parseInt( value ) * scale;
		}

		console.log( value );

		$( displayElement ).css( property, value );
	}

}( jQuery ) );
