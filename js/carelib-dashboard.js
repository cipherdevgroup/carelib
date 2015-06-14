(function( window, $, undefined ) {
	'use strict';

	// Load global JS features.
	function dashboardInit() {
		$( '#dashboard-container' ).tabs();
	}

	$( document ).ready(function() {
		dashboardInit();
	});
})( this, jQuery );
