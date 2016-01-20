<?php
/**
 * Options for displaying breadcrumbs for use in the WordPress customizer.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Customize_Settings_Breadcrumbs extends CareLib_Customize_Base {

	protected $section;

	/**
	 * Register our customizer breadcrumb options for the parent class to load.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  object  $wp_customize
	 * @return void
	 */
	public function register( $wp_customize ) {
		$this->section = "{$this->prefix}_breadcrumbs";

		$wp_customize->add_section(
			$this->section,
			array(
				'title'       => __( 'Breadcrumbs', 'carelib' ),
				'description' => __( 'Choose where you would like breadcrumbs to display.', 'carelib' ),
				'priority'    => 110,
				'capability'  => $this->capability,
			)
		);

		$priority = 10;

		foreach ( carelib_get( 'breadcrumbs' )->get_options() as $breadcrumb => $setting ) {

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
					'section'  => $this->section,
					'type'     => 'checkbox',
					'priority' => $priority++,
				)
			);
		}
	}
}
