<?php
/**
 * Loads customizer-related files (see `/inc/customize`) and sets up customizer
 * functionality.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

add_action( 'customize_controls_enqueue_scripts', 'carelib_customize_register_controls_styles', 0 );
add_action( 'init', 'carelib_customize_load_breadcrumb_settings', 0 );
add_action( 'customize_register', 'carelib_customize_load_classes', 0 );
add_action( 'customize_controls_enqueue_scripts', 'carelib_customize_register_controls_scripts', 0 );
add_action( 'customize_preview_init',             'carelib_customize_register_preview_scripts',  0 );
add_action( 'customize_preview_init',             'carelib_customize_enqueue_preview_scripts',  10 );
add_action( 'customize_controls_enqueue_scripts', 'carelib_customize_register_controls_styles', 0 );

if ( carelib_has_layouts() ) {
	add_action( 'customize_register', 'carelib_customize_register_layouts' );
}
