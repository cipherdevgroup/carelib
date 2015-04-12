/**
 * JS for handling the "Display Header Text" setting's realtime preview.
 */
( function( $ ) {
	'use strict';

	var api      = wp.customize,
		$ids = $( '#site-title, #site-description' );

	api( 'site_logo_header_text', function( value ) {
		value.bind( function() {
			$ids.toggleClass( 'screen-reader-text' );
		});
	});
})(jQuery);
