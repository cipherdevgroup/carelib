<?php
/**
 * Methods for handling admin JavaScript and CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

/**
 * Registers admin styles.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_admin_register_scripts() {
	$suffix = carelib_get_suffix();

	wp_register_script(
		'carelib-dashboard',
		carelib_get_js_uri( "carelib-dashboard{$suffix}.js" ),
		array( 'jquery-ui-tabs' ),
		CARELIB_VERSION,
		true
	);
}
