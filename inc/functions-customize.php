<?php
/**
 * Loads customizer-related files (see `/inc/customize`) and sets up customizer
 * functionality.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

# Load custom control classes.
add_action( 'customize_register', 'carelib_load_customize_classes', 0 );

# Register customizer panels, sections, settings, and/or controls.
add_action( 'customize_register', 'carelib_customize_register' );

# Register customize controls scripts/styles.
add_action( 'customize_controls_enqueue_scripts', 'carelib_customize_controls_register_scripts', 0 );
add_action( 'customize_controls_enqueue_scripts', 'carelib_customize_controls_register_styles',  0 );

# Register/Enqueue customize preview scripts/styles.
add_action( 'customize_preview_init', 'carelib_customize_preview_register_scripts', 0 );
add_action( 'customize_preview_init', 'carelib_customize_preview_enqueue_scripts' );

/**
 * Load framework-specific customize classes.
 *
 * These are classes that extend the core `WP_Customize_*` classes to provide
 * theme authors access to functionality that core doesn't handle out of the box.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_load_customize_classes( $wp_customize ) {
	require_once HYBRID_CUSTOMIZE . 'setting-array-map.php';
	require_once HYBRID_CUSTOMIZE . 'setting-image-data.php';
	require_once HYBRID_CUSTOMIZE . 'control-checkbox-multiple.php';
	require_once HYBRID_CUSTOMIZE . 'control-dropdown-terms.php';
	require_once HYBRID_CUSTOMIZE . 'control-palette.php';
	require_once HYBRID_CUSTOMIZE . 'control-radio-image.php';
	require_once HYBRID_CUSTOMIZE . 'control-select-group.php';
	require_once HYBRID_CUSTOMIZE . 'control-select-multiple.php';

	require_if_theme_supports( 'theme-layouts', HYBRID_CUSTOMIZE . 'control-layout.php' );

	// Register JS control types.
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Checkbox_Multiple' );
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Palette' );
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Radio_Image' );
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Select_Group' );
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Select_Multiple' );
}

/**
 * Register customizer panels, sections, controls, and/or settings.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_customize_register( $wp_customize ) {
	// Always add the layout section so that theme devs can utilize it.
	$wp_customize->add_section(
		'layout',
		array(
			'title'    => esc_html__( 'Layout', 'carelib' ),
			'priority' => 30,
		)
	);

	// Check if the theme supports the theme layouts customize feature.
	if ( current_theme_supports( 'theme-layouts', 'customize' ) ) {

		// Add the layout setting.
		$wp_customize->add_setting(
			'theme_layout',
			array(
				'default'           => carelib_get_default_layout(),
				'sanitize_callback' => 'sanitize_key',
				'transport'         => 'postMessage',
			)
		);

		// Add the layout control.
		$wp_customize->add_control(
			new CareLib_Customize_Control_Layout(
				$wp_customize,
				'theme_layout',
				array( 'label' => esc_html__( 'Global Layout', 'carelib' ) )
			)
		);
	}
}

/**
 * Register customizer controls scripts.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_customize_controls_register_scripts() {
	wp_register_script(
		'hybrid-customize-controls',
		HYBRID_JS . 'customize-controls' . carelib_get_min_suffix() . '.js',
		array( 'customize-controls' ),
		null,
		true
	);
}

/**
 * Register customizer controls styles.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_customize_controls_register_styles() {
	wp_register_style(
		'hybrid-customize-controls',
		HYBRID_CSS . 'customize-controls' . carelib_get_min_suffix() . '.css'
	);
}

/**
 * Register customizer preview scripts.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_customize_preview_register_scripts() {
	wp_register_script(
		'hybrid-customize-preview',
		HYBRID_JS . 'customize-preview' . carelib_get_min_suffix() . '.js',
		array( 'jquery' ),
		null,
		true
	);
}

/**
 * Register customizer preview scripts.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_customize_preview_enqueue_scripts() {
	if ( current_theme_supports( 'theme-layouts' ) ) {
		wp_enqueue_script( 'hybrid-customize-preview' );
	}
}
