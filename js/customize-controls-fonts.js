/*global _:false, _carelibThemeFontsControlsSettings:false, Backbone:false, wp:false */

(function( window, $, _, Backbone, wp, undefined ) {
	'use strict';

	var api = wp.customize,
		app = {},
		settings = _carelibThemeFontsControlsSettings;

	_.extend( app, { model: {}, view: {} } );

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	app.model.Font = Backbone.Model.extend({
		idAttribute: 'family',

		defaults: {
			family: '',
			service: '',
			stack: ''
		},

		toJSON: function() {
			return _.clone( this.attributes );
		}
	});

	app.model.Fonts = Backbone.Collection.extend({
		model: app.model.Font,

		comparator: function( model ) {
			return model.get( 'family' );
		}
	});

	app.model.FontControl = Backbone.Model.extend({
		defaults: {
			control: {},
			dropdown: 'closed',
			font: {},
			selection: {}
		},

		initialize: function() {
			this.listenTo( this, 'change:selection', this.updateFont );
			this.get( 'font' ).on( 'change', _.throttle( this.updateSetting, 50 ), this );
		},

		closeDropdown: function() {
			this.set( 'dropdown', 'closed' );
			return this;
		},

		isDropdownOpen: function() {
			return 'open' === this.get( 'dropdown' );
		},

		openDropdown: function() {
			this.set( 'dropdown', 'open' );
			return this;
		},

		resetSelection: function() {
			// The default should not be from Typekit.
			var defaultFamily = this.get( 'control' ).params.defaultFont,
				defaultFont = app.findFont( defaultFamily );

			this.closeDropdown();

			// Use a new Font model to clear the dropdown.
			this.set( 'selection', new app.model.Font() );

			// Setting the font attribute allows the default font
			// to load in the previewer.
			this.get( 'font' ).set( defaultFont.toJSON() );

			return this;
		},

		toggleDropdown: function() {
			if ( this.isDropdownOpen() ) {
				this.closeDropdown();
			} else {
				this.openDropdown();
			}
			return this;
		},

		updateFont: function() {
			var selection = this.get( 'selection' ),
				attributes = _.pick( selection.toJSON(), 'family', 'service', 'stack' );

			this.get( 'font' ).set( attributes );
			return this;
		},

		updateSetting: function() {
			var font = this.get( 'font' );
			this.get( 'control' ).setting.set( font.toJSON() );
			return this;
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	app.view.FontControl = wp.Backbone.View.extend({
		template: wp.template( 'carelib-fonts-control-font' ),

		initialize: function() {
			this.allFonts = this.collection;
			this.params = this.options.control.params;

			this.collection = new app.model.Fonts();
			this.filterFonts();

			this.listenTo( this.allFonts, 'reset', this.filterFonts );
			this.listenTo( this.allFonts, 'remove', this.maybeResetSelection );
		},

		render: function() {
			this.$el.html( this.template({
				description: this.options.description,
				label: this.options.label
			}) );

			this.views.add( '.carelib-fonts-control-content', [
				new app.view.FontDropdown({
					collection: this.collection,
					control: this.options.control
				})
			]);

			return this;
		},

		filterFonts: function() {
			var collection = this.allFonts.toJSON(),
				control = this.options.control;

			if ( this.params.tags.length ) {
				collection = _.filter( collection, function( font ) {
					return _.intersection( control.params.tags, font.tags ).length > 0 || 'google' !== font.service;
				});
			}

			if ( this.params.excludeFonts.length ) {
				collection = _.filter( collection, function( font ) {
					return -1 === control.params.excludeFonts.indexOf( font.family );
				});
			}

			this.collection.reset( collection );
		},

		maybeResetSelection: function( model ) {
			var state = this.options.control.state;

			if ( _.isEqual( model.toJSON(), state.get( 'selection' ).toJSON() ) ) {
				state.resetSelection();
			}
		}
	});

	app.view.FontDropdown = wp.Backbone.View.extend({
		className: 'carelib-fonts-dropdown',
		tagName: 'div',

		initialize: function() {
			this.state = this.options.control.state;
			this.listenTo( this.state, 'change:dropdown', this.updateOpenClass );
			this.listenTo( this.state, 'change:selection', this.updateSelectionClass );
		},

		render: function() {
			this.views.add([
				new app.view.FontDropdownToggle({
					control: this.options.control
				}),
				new app.view.FontDropdownResetButton({
					control: this.options.control
				}),
				new app.view.FontList({
					collection: this.collection,
					control: this.options.control
				})
			]);

			this.updateSelectionClass();
			return this;
		},

		updateDropdownPositionAndHeight: function() {
			var topSpace,
				vh = window.innerHeight || $( window ).height(),
				$container = this.$el.closest( '.accordion-section-content' ),
				$list = this.$el.find( '.carelib-fonts-list' ),
				bottomOffset = vh - $container.offset().top - $container.outerHeight(),
				bottomSpace = vh - this.$el.offset().top - this.$el.outerHeight() - bottomOffset;

			if ( bottomSpace >= 120 ) {
				this.$el.removeClass( 'is-reversed' );
				// 2 accounts for the list border.
				$list.css( 'max-height', bottomSpace - 2 + 'px' );
			} else {
				topSpace = this.$el.offset().top - $container.offset().top;
				this.$el.addClass( 'is-reversed' );
				$list.css( 'max-height', topSpace - 12 + 'px' );
			}
		},

		updateOpenClass: function() {
			var $container = this.$el.closest( '.accordion-section-content' );

			if ( this.state.isDropdownOpen() ) {
				this.updateDropdownPositionAndHeight();
				$container.css( 'overflow', 'hidden' );
				this.$el.addClass( 'is-open' );
			} else {
				$container.css( 'overflow-y', 'auto' );
				this.$el.removeClass( 'is-open' );
			}
		},

		updateSelectionClass: function() {
			var family = this.state.get( 'selection' ).get( 'family' );
			this.$el.toggleClass( 'has-selection', '' !== family );
		}
	});

	app.view.FontDropdownToggle = wp.Backbone.View.extend({
		className: 'carelib-fonts-dropdown-toggle',
		tagName: 'span',

		events: {
			'click': 'toggleDropdown'
		},

		initialize: function() {
			this.state = this.options.control.state;
			this.listenTo( this.state, 'change:selection', this.render );
		},

		render: function() {
			var family = this.state.get( 'selection' ).get( 'family' ),
				stack = this.state.get( 'selection' ).get( 'stack' );

			if ( '' !== family ) {
				this.$el.text( family ).css( 'font-family', stack );
			} else {
				this.$el.text( settings.l10n.defaultFont ).css( 'font-family', '' );
			}

			return this;
		},

		toggleDropdown: function() {
			this.state.toggleDropdown();
		}
	});

	app.view.FontDropdownResetButton = wp.Backbone.View.extend({
		className: 'carelib-fonts-dropdown-reset-button',
		tagName: 'button',

		events: {
			'click': 'resetSelection'
		},

		render: function() {
			this.$el.text( settings.l10n.reset );
			return this;
		},

		resetSelection: function( e ) {
			e.preventDefault();
			this.options.control.state.resetSelection();
		}
	});

	app.view.FontList = wp.Backbone.View.extend({
		className: 'carelib-fonts-list',
		tagName: 'ul',

		initialize: function() {
			this.listenTo( this.collection, 'reset', this.render );
		},

		render: function() {
			this.$el.empty();
			this.collection.each( this.addFont, this );
			return this;
		},

		addFont: function( font ) {
			var fontView = new app.view.FontListItem({
					control: this.options.control,
					model: font
				});

			this.$el.append( fontView.render().el );
		}
	});

	app.view.FontListItem = wp.Backbone.View.extend({
		className: 'carelib-fonts-list-item',
		tagName: 'li',

		events: {
			'click': 'updateSelection',
		},

		initialize: function() {
			this.state = this.options.control.state;
			this.listenTo( this.state, 'change:selection', this.toggleSelectedClass );
		},

		render: function() {
			var family = this.model.get( 'family' ),
				service = this.model.get( 'service' );

			this.$el
				.text( family )
				.css( 'font-family', this.model.get( 'stack' ) )
				.addClass( 'is-' + service );

			this.toggleSelectedClass();
			return this;
		},

		toggleSelectedClass: function() {
			var family = this.model.get( 'family' ),
				selected = this.state.get( 'selection' ).get( 'family' );

			this.$el.toggleClass( 'is-selected', family === selected );
		},

		updateSelection: function() {
			var family = this.model.get( 'family' );
			this.state.closeDropdown().set( 'selection', app.findFont( family ) );
		}
	});

	/**
	 * ========================================================================
	 * SETUP
	 * ========================================================================
	 */

	app.fonts = new app.model.Fonts( settings.fonts );

	// @todo What about fonts that are the same across services?
	app.findFont = function( family, attributes ) {
		var font = app.fonts.findWhere({ family: family });
		attributes = attributes || {};

		return font || new app.model.Font( attributes );
	};

	app.loadGoogleFonts = function() {
		_.each( app.fonts.where({ service: 'google' }), function( font ) {
			var family = font.get( 'family' );

			WebFont.load({
				google: {
					families: [ family ],
					text: family
				}
			});
		});
	};

	app.loadTypekitFonts = function( kitId ) {
		var url = 'https://typekit.com/api/v1/json/kits/%s/published?callback=?';

		kitId = kitId.replace( /[^a-z0-9]+/, '' );
		if ( '' === kitId ) {
			return;
		}

		return $.ajax( url.replace( '%s', kitId ), {
			dataType: 'json'
		}).done(function( response ) {
			// Add fonts from the kit to the global font collection.
			_.each( response.kit.families, function( font ) {
				app.fonts.add({
					family: font.name, // @todo Sanitize this.
					stack: font.css_stack, // @todo Sanitize this.
					service: 'typekit'
				});
			});

			WebFont.load({
				typekit: { id: kitId }
			});

			// Trigger reset to re-render the dropdowns.
			app.fonts.trigger( 'reset' );
		});
	};

	app.unloadTypekitFonts = function() {
		var typekitFonts = app.fonts.where({ service: 'typekit' });
		app.fonts.remove( typekitFonts );
	};

	api.sectionConstructor['carelib-fonts'] = api.Section.extend({
		googleFontsLoaded: false,
		typekitLoadedKits: [],

		attachEvents: function() {
			var $buttons, $content, $help, $helpToggle,
				$options, $optionsToggle;

			this.expanded.bind( _.bind( this.maybeLoadFonts, this ) );

			$buttons = this.container.find( '.carelib-fonts-section-toggle' );
			$content = this.container.find( '.carelib-fonts-section-content' );

			$buttons.on( 'click keydown', function( e ) {
				var $button = $( this ),
					$target = $content.filter( $button.data( 'target' ) );

				if ( api.utils.isKeydownButNotEnterEvent( e ) ) {
					return;
				}

				e.preventDefault();

				if ( 'true' === $button.attr( 'aria-expanded' ) ) {
					$target.slideUp( 'fast' );
					$button.attr( 'aria-expanded', 'false' );
				} else {
					$target.slideDown( 'fast' );
					$button.attr( 'aria-expanded', 'true' );

					$content.not( $target ).slideUp( 'fast' );
					$buttons.not( $button ).attr( 'aria-expanded', 'false' );
				}
			});

			// Update the Typekit Kit ID setting when the field value changes.
			$( '#carelib-fonts-option-typekit-id' ).on( 'change', this.updateTypekitId );

			api.Section.prototype.attachEvents.apply( this, arguments );
		},

		/**
		 * Load fonts the first time the section is expanded.
		 */
		maybeLoadFonts: function() {
			var typekitKitId = api( 'carelib_fonts_typekit_id' )();

			if ( ! this.googleFontsLoaded ) {
				app.loadGoogleFonts();
				this.googleFontsLoaded = true;
			}

			if ( '' !== typekitKitId && -1 === this.typekitLoadedKits.indexOf( typekitKitId ) ) {
				app.loadTypekitFonts( typekitKitId );
				this.typekitLoadedKits.push( typekitKitId );
			}
		},

		updateTypekitId: function( e ) {
			var $field = $( this ),
				$container = $field.parent(),
				$spinner = $container.find( '.spinner' );

			app.unloadTypekitFonts();

			if ( '' === $field.val() ) {
				api( 'carelib_fonts_typekit_id' ).set( '' );
				app.fonts.trigger( 'reset' );
				return;
			}

			$spinner.addClass( 'is-active' );

			app.loadTypekitFonts( $field.val() )
				.done(function( response ) {
					api( 'carelib_fonts_typekit_id' ).set( $field.val() );
				})
				.always(function() {
					$spinner.removeClass( 'is-active' );
				});
		}
	});

	api.controlConstructor['carelib-font'] = api.Control.extend({
		ready: function() {
			var selection = app.findFont( this.params.value.family, this.params.value );

			// Don't select anything if the default font is active.
			if ( this.params.defaultFont === selection.get( 'family' ) ) {
				selection = new app.model.Font();
			}

			this.state = new app.model.FontControl({
				control: this,
				font: new app.model.Font( this.params.value ),
				selection: selection
			});

			// Create the control view.
			this.view = new app.view.FontControl({
				collection: app.fonts,
				control: this,
				description: this.params.description,
				el: this.container,
				label: this.params.label
			});

			this.view.render();
		}
	});
})( window, jQuery, _, Backbone, wp );
