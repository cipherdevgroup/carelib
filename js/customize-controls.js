(function( $, api ) {
	'use strict';

	/**
	 * Palette Control
	 */
	api.controlConstructor.palette = api.Control.extend({
		ready: function() {
			var control = this;

			// Adds a `.selected` class to the label of checked inputs.
			$( 'input:radio:checked', control.container ).parent( 'label' ).addClass( 'selected' );

			$( 'input:radio', control.container ).change(
				function() {
					// Removes the `.selected` class from other labels and adds it to the new one.
					$( 'label.selected', control.container ).removeClass( 'selected' );
					$( this ).parent( 'label' ).addClass( 'selected' );

					control.setting.set( $( this ).val() );
				}
			);
		}
	});

	/**
	 * Radio Image Control
	 */
	api.controlConstructor['radio-image'] = api.Control.extend({
		ready: function() {
			var control = this;

			$( 'input:radio', control.container ).change(
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		}
	});
})( jQuery, wp.customize );
