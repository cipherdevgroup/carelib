/*global _:false, carelibFontsPreviewSettings:false, Backbone:false, wp:false */

(function( window, $, _, Backbone, wp, undefined ) {
	'use strict';

	var api = wp.customize,
		app = {},
		loadedFonts = [],
		settings = carelibFontsPreviewSettings;

	_.extend( app, { model: {}, view: {} } );

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	app.model.Group = Backbone.Model.extend({
		defaults: {
			id: '',
			family: '',
			selector: '',
			service: 'google',
			stack: '',
			variations: ''
		},

		initialize: function() {
			this.listenTo( this, 'change:family', this.loadFont );
		},

		loadFont: function() {
			var config = {
					events: false,
					classes: false
				},
				family = this.get( 'family' ),
				service = this.get( 'service' ),
				typekitKitId = api( 'carelib_fonts_typekit_id' )();

			if ( 'google' === service && '' !== family ) {
				config.google = { families: [ this.getGoogleFamilyDefinition() ] };
			}

			if ( 'typekit' === service && '' !== typekitKitId ) {
				config.typekit = {
					id: typekitKitId
				};
			}

			app.loadFont( config );
		},

		getGoogleFamilyDefinition: function() {
			var family = this.get( 'family' ),
				variations = this.get( 'variations' );

			if ( '' !== variations ) {
				family += ':' + variations;
			}

			if ( '' !== settings.subsets && 'latin' !== settings.subsets ) {
				family += ':' + settings.subsets;
			}

			return family;
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	app.view.Style = wp.Backbone.View.extend({
		tagName: 'style',

		initialize: function() {
			this.model = this.options.model;
			this.listenTo( this.model, 'change:stack', this.render );
		},

		render: function() {
			var css = '',
				stack = this.model.get( 'stack' ),
				selector = this.model.get( 'selector' );

			if ( '' !== stack ) {
				css  = selector + ' { font-family: ' + stack + ';}';
			}

			this.$el.html( css );
			return this;
		}
	});

	/**
	 * ========================================================================
	 * SETUP
	 * ========================================================================
	 */

	app.loadFont = function( config ) {
		var configJson = JSON.stringify( config );

		// Load new fonts only.
		if ( config && -1 === loadedFonts.indexOf( configJson ) ) {
			WebFont.load( config );
			loadedFonts.push( configJson );
		}
	};

	api.bind( 'preview-ready', function() {
		var $head = $( 'head' );

		_.each( settings.groups, function( group ) {
			var style,
				model = new app.model.Group( group ),
				value = api( group.id + '_font' )();

			// Use saved font properties from the corresponding setting.
			if ( value ) {
				model.set( value );
			}

			style = new app.view.Style({
				model: model
			});

			$head.append( style.render().$el );

			api( group.id + '_font', function( setting ) {
				setting.bind(function( value ) {
					model.set( value );
				});
			});
		});
	});
})( window, jQuery, _, Backbone, wp );
