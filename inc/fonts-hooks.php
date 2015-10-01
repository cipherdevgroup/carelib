<?php
/**
 * Hooks for the custom fonts feature.
 *
 * Based on Cedaro's custom fonts feature.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Class for hooking methods to support the fonts feature.
 *
 * @package CareLib
 * @since   0.2.0
 */
class CareLib_Fonts_Hooks extends CareLib_Fonts {
	/**
	 * Add customize register actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function customize_register( CareLib_Customize_Setup_Register $class ) {
		add_action( 'customize_register', array( $class, 'customize_register_fonts' ) );

		return $this;
	}

	/**
	 * Add customize scripts actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function customize_scripts( CareLib_Customize_Setup_Scripts $class ) {
		add_action( 'customize_controls_enqueue_scripts',      array( $class, 'enqueue_fonts_controls' ) );
		add_action( 'customize_preview_init',                  array( $class, 'enqueue_fonts_preview' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $class, 'print_fonts_templates' ) );

		return $this;
	}

	/**
	 * Add public scripts actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function public_scripts( CareLib_Public_Scripts $class ) {
		add_action( 'init', array( $class, 'register_fonts_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $class, 'enqueue_fonts_scripts' ) );

		return $this;
	}

	/**
	 * Register public styles actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function public_styles( CareLib_Public_Styles $class ) {
		add_action( 'wp_enqueue_scripts', array( $class, 'enqueue_fonts_styles' ),    10 );
		add_action( 'wp_enqueue_scripts', array( $class, 'add_inline_fonts_styles' ), 15 );

		return $this;
	}

	/**
	 * Add TinyMCE actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function tinymce( CareLib_TinyMCE $class ) {
		add_filter( 'tiny_mce_before_init',             array( $class, 'register_tinymce_settings' ) );
		add_filter( 'mce_external_plugins',             array( $class, 'register_tinymce_plugin' ) );
		add_action( 'init',                             array( $class, 'add_editor_style' ) );
		add_action( 'mce_css',                          array( $class, 'add_dynamic_styles' ) );
		add_action( 'wp_ajax_carelib-fonts-editor-css', array( $class, 'output_dynamic_styles' ) );

		return $this;
	}
}
