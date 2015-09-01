(function ( $, window, undefined ) {
	'use strict';

	$( '.share-url-button' ).click(function () {
		var el	= $( this );
		var old = el.text();

		el.text( 'Copied' );
		setTimeout( function () { el.text( old ); }, 1500 );
	});

	$( '.row-actions' ).mouseenter (function () {
		$(this).addClass( 'is-active' );
	});

	$( '.row-actions' ).mouseleave(function () {
		$(this).removeClass( 'is-active' );
	});

})( jQuery, window );
