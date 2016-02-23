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

/**
 * Load library-specific customize classes.
 *
 * These are classes that extend the core `WP_Customize_*` classes to provide
 * theme authors access to functionality that core doesn't handle out of the box.
 *
 * @since  1.0.0
 * @access public
 * @param  object $wp_customize The WordPress customizer API object.
 * @return void
 */
function carelib_customize_load_classes( $wp_customize ) {
	$wp_customize->register_control_type( 'CareLib_Customize_Control_Radio_Image' );
}

/**
 * Register customizer panels, sections, controls, and/or settings.
 *
 * @since  1.0.0
 * @access public
 * @param  object $wp_customize The WordPress customizer API object.
 * @return void
 */
function carelib_customize_register_layouts( $wp_customize ) {
	if ( ! carelib_has_layouts() ) {
		return;
	}

	// Always add the layout section so that theme devs can utilize it.
	$wp_customize->add_section(
		'layout',
		array(
			'title'           => esc_html__( 'Layout', 'carelib' ),
			'priority'        => 30,
			'active_callback' => 'carelib_allow_layout_control',
		)
	);

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
