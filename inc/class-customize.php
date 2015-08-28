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

class CareLib_Customize  {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Script suffix to determine whether or not to load minified scripts.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $suffix;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
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
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		# Load custom control classes.
		add_action( 'customize_register',                 array( $this, 'load_customize_classes' ),    0 );
		add_action( 'init',                               array( $this, 'load_customizer_settings' ),  0 );
		add_action( 'customize_register',                 array( $this, 'customize_register' ),       10 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls_scripts' ), 0 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls_styles' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'register_preview_scripts' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'enqueue_preview_scripts' ),  10 );
	}

	/**
	 * Return the path to the CareLib customize directory with a trailing slash.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  string
	 */
	protected function get_dir( $path = '' ) {
		return carelib()->get_dir( 'customize/' ) . $path;
	}

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
	public function load_customize_classes( $wp_customize ) {
		require_once $this->get_dir( 'setting-array-map.php' );
		require_once $this->get_dir( 'setting-image-data.php' );
		require_once $this->get_dir( 'control-radio-image.php' );
		require_once $this->get_dir( 'control-checkbox-multiple.php' );
		require_once $this->get_dir( 'control-dropdown-terms.php' );
		require_once $this->get_dir( 'control-layout.php' );
		require_once $this->get_dir( 'control-palette.php' );
		require_once $this->get_dir( 'control-select-group.php' );
		require_once $this->get_dir( 'control-select-multiple.php' );
		require_once $this->get_dir( 'control-site-logo.php' );

		// Register JS control types.
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Checkbox_Multiple' );
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Palette' );
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Radio_Image' );
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Select_Group' );
		$wp_customize->register_control_type( 'CareLib_Customize_Control_Select_Multiple' );
	}


	public function load_customizer_settings() {
		if ( ! carelib()->is_customizer_preview() ) {
			return;
		}
		require_once $this->get_dir( 'customizer-base.php' );
		require_once $this->get_dir( 'settings-site-logo.php' );

		carelib_class( 'settings-site-logo' );

		if ( carelib_class( 'breadcrumbs' )->plugin_is_active() ) {
			require_once $this->get_dir( 'settings-breadcrumbs.php' );
			carelib_class( 'settings-breadcrumbs' );
		}
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
				'title'    => esc_html__( 'Layout', 'carelib' ),
				'priority' => 30,
			)
		);

		// Add the layout setting.
		$wp_customize->add_setting(
			'theme_layout',
			array(
				'default'           => carelib_class( 'layouts' )->get_default_layout(),
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
	 * Register customizer controls scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_controls_scripts() {
		wp_register_script(
			"{$this->prefix}-customize-controls",
			carelib()->get_uri( "js/customize-controls{$this->suffix}.js" ),
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
	public function register_controls_styles() {
		wp_register_style(
			"{$this->prefix}-customize-controls",
			carelib()->get_uri( "css/customize-control{$this->suffix}.css" )
		);
	}

	/**
	 * Register customizer preview scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_preview_scripts() {
		wp_register_script(
			"{$this->prefix}-customize-preview",
			carelib()->get_uri( "js/customize-preview{$this->suffix}.js" ),
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
	public function enqueue_preview_scripts() {
		wp_enqueue_script( "{$this->prefix}-customize-preview" );
	}
}
