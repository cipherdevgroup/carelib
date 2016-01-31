<?php
/**
 * Options for displaying breadcrumbs for use in the WordPress customizer.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

function carelib_customize_load_breadcrumb_settings() {
	if ( carelib_breadcrumb_plugin_is_active() ) {
		add_action( 'customize_register', 'carelib_register_breadcrumb_settings', 15 );
	}
}

/**
 * Register our customizer breadcrumb options for the parent class to load.
 *
 * @since  0.1.0
 * @access public
 * @param  object  $wp_customize
 * @return void
 */
function carelib_register_breadcrumb_settings( $wp_customize ) {
	$section = "{$GLOBALS['carelib_prefix']}_breadcrumbs";

	$wp_customize->add_section(
		$section,
		array(
			'title'       => __( 'Breadcrumbs', 'carelib' ),
			'description' => __( 'Choose where you would like breadcrumbs to display.', 'carelib' ),
			'priority'    => 110,
			'capability'  => 'edit_theme_options',
		)
	);

	$priority = 10;

	foreach ( carelib_get_breadcrumb_options() as $breadcrumb => $setting ) {

		$wp_customize->add_setting(
			$breadcrumb,
			array(
				'default'           => $setting['default'],
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			$breadcrumb,
			array(
				'label'    => $setting['label'],
				'section'  => $section,
				'type'     => 'checkbox',
				'priority' => $priority++,
			)
		);
	}
}
