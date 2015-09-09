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

class CareLib_Customize_Setup_Scripts extends CareLib_Scripts {

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
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls' ), 0 );
		add_action( 'customize_preview_init',             array( $this, 'register_preview' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'enqueue_preview' ),  10 );
	}

	/**
	 * Register customizer controls scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_controls() {
		wp_register_script(
			'carelib-customize-controls',
			$this->js_uri( "customize-controls{$this->suffix}.js" ),
			array( 'customize-controls' ),
			$this->version,
			true
		);
		wp_register_script(
			'site-logo-control',
			esc_url( $this->js_uri( "site-logo-control{$this->suffix}.js" ) ),
			array( 'media-views', 'customize-controls', 'underscore' ),
			'',
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
	public function register_preview() {
		wp_register_script(
			'carelib-customize-preview',
			$this->js_uri( "customize-preview{$this->suffix}.js" ),
			array( 'jquery' ),
			$this->version,
			true
		);
		wp_register_script(
			'site-logo-preview',
			esc_url( $this->js_uri( "site-logo-preview{$this->suffix}.js" ) ),
			array( 'media-views' ),
			$this->version,
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
	public function enqueue_preview() {
		wp_enqueue_script( 'carelib-customize-preview' );
	}

}
