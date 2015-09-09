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

class CareLib_Customize_Setup_Styles extends CareLib_Styles {

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
	}

	/**
	 * Register customizer controls styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_controls() {
		wp_register_style(
			'carelib-customize-controls',
			$this->css_uri( "customize-controls{$this->suffix}.css" ),
			array(),
			$this->version
		);
	}

}
