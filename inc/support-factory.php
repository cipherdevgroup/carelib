<?php
/**
 * Build all the default classes necessary for the library to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Support_Factory extends CareLib_Factory {

	/**
	 * Method to fire all actions within the class.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		add_action( 'after_setup_theme', array( $this, 'build_supported' ), 25 );
	}

	/**
	 * Add conditional classes based on theme support.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  array $classes the existing default library classes
	 * @return array $classes the modified classes based on theme support
	 */
	protected function get_supported_classes() {
		$classes = array();
		if ( current_theme_supports( 'theme-layouts' ) ) {
			$classes[] = 'layouts';
		}
		if ( is_admin() ) {
			if ( current_theme_supports( 'theme-dashboard' ) ) {
				$classes[] = 'admin-dashboard';
			}
		} else {
			if ( current_theme_supports( 'site-logo' ) && ! function_exists( 'jetpack_the_site_logo' ) ) {
				$classes[] = 'site-logo';
			}
		}

		return $classes;
	}

	/**
	 * Loads and instantiates all functionality which requires theme support.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function build_supported() {
		foreach ( (array) $this->get_supported_classes() as $class ) {
			self::get( $class )->run();
		}
	}

}
