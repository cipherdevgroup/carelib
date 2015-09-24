<?php
/**
 * Modifications to TinyMCE, the default WordPress editor.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class CareLib_TinyMCE {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Author_Box::$wp_hooks
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_filter( 'mce_buttons',          array( $this, 'add_styleselect' ),     99 );
		add_filter( 'mce_buttons_2',        array( $this, 'disable_styleselect' ), 99 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_formats' ),     99 );
	}

	/**
	 * Register actions and filters for the CareLib Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function fonts_hooks() {
		add_filter( 'tiny_mce_before_init',             array( $this, 'register_tinymce_settings' ) );
		add_filter( 'mce_external_plugins',             array( $this, 'register_tinymce_plugin' ) );
		add_action( 'init',                             array( $this, 'add_editor_style' ) );
		add_action( 'mce_css',                          array( $this, 'add_dynamic_styles' ) );
		add_action( 'wp_ajax_carelib-fonts-editor-css', array( $this, 'output_dynamic_styles' ) );
	}

	/**
	 * Add styleselect button to the end of the first row of TinyMCE buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function add_styleselect( $buttons ) {
		// Get rid of styleselect if it's been added somewhere else.
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		array_push( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Remove styleselect button if it's been added to the second row of TinyMCE
	 * buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function disable_styleselect( $buttons ) {
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		return $buttons;
	}

	/**
	 * Add our custom CareLib styles to the styleselect dropdown button.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array existing TinyMCE arguments
	 * @return $args array modified TinyMCE arguments
	 * @see    http://wordpress.stackexchange.com/a/128950/9844
	 */
	public function tinymce_formats( $args ) {
		$formats = apply_filters( carelib()->get_prefix() . '_tiny_mce_formats',
			array(
				array(
					'title'    => __( 'Drop Cap', 'carelib' ),
					'inline'   => 'span',
					'classes'  => 'dropcap',
				),
				array(
					'title'    => __( 'Pull Quote Left', 'carelib' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignleft',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Pull Quote Right', 'carelib' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignright',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Intro Paragraph', 'carelib' ),
					'selector' => 'p',
					'classes'  => 'intro-paragraph',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Call to Action', 'carelib' ),
					'block'    => 'div',
					'classes'  => 'call-to-action',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Feature Box', 'carelib' ),
					'block'    => 'div',
					'classes'  => 'feature-box',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Code Block', 'carelib' ),
					'format'   => 'pre',
				),
				array(
					'title'    => __( 'Buttons', 'carelib' ),
					'items'    => array(
						array(
							'title'    => __( 'Standard', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Standard Block', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button block',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action Block', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta block',
							'exact'    => true,
						),
					),
				),
			)
		);
		// Merge with any existing formats which have been added by plugins.
		if ( ! empty( $args['style_formats'] ) ) {
			$existing_formats = json_decode( $args['style_formats'] );
			$formats = array_merge( $formats, $existing_formats );
		}

		$args['style_formats'] = wp_json_encode( $formats );

		return $args;
	}

	/**
	 * Register assets for enqueueing on demand.
	 *
	 * @since 0.2.0
	 */
	public function add_editor_style() {
		if ( $url = carelib_get( 'fonts' )->get_google_fonts_url() ) {
			add_editor_style( $url );
		}
	}

	/**
	 * Register TinyMCE settings.
	 *
	 * Adds the Typekit Kit ID to the settings for loading in the editor.
	 *
	 * @since 0.2.0
	 *
	 * @param  array $settings TinyMCE settings.
	 * @return array
	 */
	public function register_tinymce_settings( $settings ) {
		$settings['carelibFontsTypekitId'] = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		return $settings;
	}

	/**
	 * Register a TinyMCE plugin for loading custom fonts.
	 *
	 * Loads a Typekit Kit.
	 *
	 * @param  array $external_plugins List of external plugins.
	 * @return array
	 */
	public function register_tinymce_plugin( $external_plugins ) {
		if ( carelib_get( 'fonts' )->is_typekit_active() ) {
			$external_plugins['carelibfonts'] = carelib()->get_uri( 'js/tinymce-fonts.js' );
		}

		return $external_plugins;
	}

	/**
	 * Register a dynamic style sheet URL for the editor.
	 *
	 * This needs to be registered after the main theme style sheet.
	 *
	 * @since 0.2.0
	 *
	 * @param string $stylesheets Comma-separated list of style sheet URLs.
	 */
	public function add_dynamic_styles( $stylesheets ) {
		$stylesheets .= ',' . add_query_arg( 'action', 'carelib-fonts-editor-css', admin_url( 'admin-ajax.php' ) );
		return $stylesheets;
	}

	/**
	 * Output editor styles for custom fonts.
	 *
	 * @since 0.2.0
	 *
	 * @link http://wordpress.stackexchange.com/a/120835
	 */
	public function output_dynamic_styles() {
		header( 'Content-Type: text/css' );
		echo carelib_get( 'fonts' )->get_css(); // WPCS: XSS OK.
		exit;
	}

}
