(function ( $, window, undefined ) {
	'use strict';

	var $assign_by_taxonomy = $( '#apple-news-sections-by-taxonomy' );

	// Listen for clicks on the submit button.
	$( '#apple-news-publish-submit' ).click(function ( e ) {
		$( '#apple-news-publish-action' ).val( apple_news_meta_boxes.publish_action );
		$( '#post' ).submit();
	});

	// Listen for changes to the "assign by taxonomy" checkbox.
	if ( $assign_by_taxonomy.length ) {
		$assign_by_taxonomy.on( 'change', function () {
			if ( $( this ).is( ':checked' ) ) {
				$( '.apple-news-sections' ).hide();
			} else {
				$( '.apple-news-sections' ).show();
			}
		} ).change();
	}

	// Set up initial state of collapsable blocks.
	$( '.apple-news-metabox-section-collapsable' ).each( function () {
		var $this = $( this );

		// Set up initial collapsed state.
		$this.addClass( 'apple-news-metabox-section-collapsed' );

		// Add the expand controls.
		var $heading = $this.find( 'h3' ).first().clone();
		$heading.addClass( 'apple-news-metabox-section-control' );
		$heading.insertBefore( $this ); // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.insertBefore

		// Add the close controls.
		$this.prepend( // phpcs:ignore WordPressVIPMinimum.JS.HTMLExecutingFunctions.prepend
			$( '<div></div>' ).addClass( 'apple-news-metabox-section-close' )
		);
	} );

	// Set up listener for clicks on expand controls.
	$( '.apple-news-metabox-section-control' ).on( 'click', function () {
		$( this )
			.next( '.apple-news-metabox-section-collapsable' )
			.addClass( 'apple-news-metabox-section-visible' )
			.removeClass( 'apple-news-metabox-section-collapsed' );
	} );

	// Set up listener for clicks on close controls.
	$( '.apple-news-metabox-section-close' ).on( 'click', function () {
		$( this )
			.parent()
			.addClass( 'apple-news-metabox-section-collapsed' )
			.removeClass( 'apple-news-metabox-section-visible' );
	} );

	// Metadata.
  var $metadata = $( '#apple-news-metabox-metadata' );

	// Set up listener for clicks on the Remove metadata button.
  $metadata.on( 'click', '.apple-news-metadata-remove', function ( event ) {
    event.preventDefault();
    $( this ).parent().remove();
  } );

  // Set up listener for clicks on the Add Metadata button.
  $metadata.on( 'click', '.apple-news-metadata-add', function ( event ) {
    event.preventDefault();
    var $keys = $( '[name="apple_news_metadata_keys[]"]' );
    var index = 0;
    if ( $keys.length ) {
      var matches = $keys[ $keys.length - 1 ].id.match( /[0-9]+/ );
      if ( matches[0] ) {
        index = parseInt( matches[0], 10 ) + 1;
      }
    }
    /* phpcs:disable WordPressVIPMinimum.JS.StringConcat.Found, WordPressVIPMinimum.JS.HTMLExecutingFunctions.insertBefore */
    $(
      "<div>" +
      "<label for=\"apple-news-metadata-key-" + index + "\">Key<br />" +
      "<input id=\"apple-news-metadata-key-" + index + "\" name=\"apple_news_metadata_keys[]\" type=\"text\" value=\"\" />" +
      "</label>" +
      "<label for=\"apple-news-metadata-type-" + index + "\">Type<br />" +
      "<select id=\"apple-news-metadata-type-" + index + "\" name=\"apple_news_metadata_types[]\">" +
      "<option value=\"\"></option>" +
      "<option value=\"string\">string</option>" +
      "<option value=\"boolean\">boolean</option>" +
      "<option value=\"number\">number</option>" +
      "<option value=\"array\">array</option>" +
      "</select>" +
      "</label>" +
      "<label for=\"apple-news-metadata-value-" + index + "\">Value<br />" +
      "<input id=\"apple-news-metadata-value-" + index + "\" name=\"apple_news_metadata_values[]\" type=\"text\" value=\"\" />" +
      "</label>" +
      "<button class=\"button-secondary apple-news-metadata-remove\">Remove</button>" +
			"</div>" +
      "</div>"
    ).insertBefore( $( this ) );
    /* phpcs:enable */
  } );
})( jQuery, window );
