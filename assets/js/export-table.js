(function ( $, window, undefined ) {
  'use strict';

  new ZeroClipboard( $('.share-url-button') );

  $('.row-actions').mouseenter(function () {
    $(this).addClass( 'is-active' );
  });

  $('.row-actions').mouseleave(function () {
    $(this).removeClass( 'is-active' );
  });

})( jQuery, window );
