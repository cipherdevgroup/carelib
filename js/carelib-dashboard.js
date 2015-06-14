window.flagshipDashboard = window.flagshipDashboard || {};

(function( window, $, undefined ) {
	'use strict';

	var flagshipDashboard = window.flagshipDashboard;

	$.extend( flagshipDashboard, {

		// Load global JS features.
		init: function() {
			$( '#dashboard-tabs' ).tabs();
		}

	});

	// Document ready.
	jQuery(function() {
		flagshipDashboard.init();
	});

})( this, jQuery );
