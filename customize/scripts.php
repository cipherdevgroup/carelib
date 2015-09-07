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

class CareLib_Customize_Scripts {

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
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls_scripts' ), 0 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls_styles' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'register_preview_scripts' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'enqueue_preview_scripts' ),  10 );
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
			'carelib-customize-controls',
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
			'carelib-customize-controls',
			carelib()->get_uri( "css/customize-controls{$this->suffix}.css" )
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
			'carelib-customize-preview',
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
		wp_enqueue_script( 'carelib-customize-preview' );
	}

}
