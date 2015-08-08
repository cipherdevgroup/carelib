<?php
/**
 * Helper functions for working with the WordPress menu system.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Menu Class.
 */
class CareLib_Menu {

	/**
	 * Function for grabbing a WP nav menu theme location name.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $location
	 * @return string
	 */
	function get_location_name( $location ) {
		$locations = get_registered_nav_menus();
		return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
	}

	/**
	 * Get a specified menu template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function template( $name = null ) {
		$templates = array();
		if ( '' !== $name ) {
			$templates[] = "templates/menu-{$name}.php";
			$templates[] = "templates/menu/{$name}.php";
		}
		$templates[] = 'templates/menu.php';
		$templates[] = 'templates/menu/menu.php';
		locate_template( $templates, true );
	}

}