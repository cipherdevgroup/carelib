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

class CareLib_Customize_Setup_Settings {

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
		add_action( 'init', array( $this, 'load_customizer_settings' ), 0 );
	}

	public function load_customizer_settings() {
		if ( current_theme_supports( 'site-logo' ) && ! function_exists( 'jetpack_the_site_logo' ) ) {
			carelib_get( 'customize-settings-site-logo' );
		}

		if ( carelib_get( 'breadcrumbs' )->plugin_is_active() ) {
			carelib_get( 'customize-settings-breadcrumbs' );
		}
	}

}
