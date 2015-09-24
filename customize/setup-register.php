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

class CareLib_Customize_Setup_Register {

	protected $layouts;

	public function __construct() {
		$this->layouts = carelib_get( 'layouts' );
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'customize_register', array( $this, 'load_customize_classes' ), 0 );
		add_action( 'customize_register', array( $this, 'customize_register' ),    10 );
	}

	/**
	 * Register actions and filters for the CareLib Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function fonts_hooks() {
		add_action( 'customize_register', array( $this, 'customize_register_fonts' ) );
	}

	/**
	 * Load library-specific customize classes.
	 *
	 * These are classes that extend the core `WP_Customize_*` classes to provide
	 * theme authors access to functionality that core doesn't handle out of the box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function load_customize_classes( $wp_customize ) {
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Palette' );
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Radio_Image' );
	}

	/**
	 * Register customizer panels, sections, controls, and/or settings.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function customize_register( $wp_customize ) {
		// Always add the layout section so that theme devs can utilize it.
		$wp_customize->add_section(
			'layout',
			array(
				'title'           => esc_html__( 'Layout', 'carelib' ),
				'priority'        => 30,
				'active_callback' => array( $this->layouts, 'allow_layout_control' ),
			)
		);

		// Add the layout setting.
		$wp_customize->add_setting(
			'theme_layout',
			array(
				'default'           => $this->layouts->get_default_layout(),
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

	/**
	 * Register Customizer settings and controls.
	 *
	 * @since 0.2.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function customize_register_fonts( $wp_customize ) {
		$wp_customize->register_section_type( 'CareLib_Customize_Section_Fonts' );

		$wp_customize->add_section( new CareLib_Customize_Section_Fonts( $wp_customize, 'carelib_fonts', array(
			'title'       => esc_html__( 'Fonts', 'carelib' ),
			'priority'    => 50,
		) ) );

		$wp_customize->add_setting( 'carelib_fonts_typekit_id', array(
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		foreach ( $this->text_groups as $group ) {
			$id = $group['id'] . '_font';

			$wp_customize->add_setting( $id, array(
				'sanitize_callback' => array( $this, 'sanitize_font' ),
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new CareLib_Customize_Control_Font( $wp_customize, $id, array(
				'label'         => $group['label'],
				'description'   => $group['description'],
				'section'       => 'carelib_fonts',
				'settings'      => $id,
				'default_font'  => $group['family'],
				'exclude_fonts' => $group['exclude'],
				'tags'          => $group['tags'],
			) ) );
		}
	}

}
