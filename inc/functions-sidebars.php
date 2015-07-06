<?php
/**
 * Helper functions for working with the WordPress sidebar system. Currently, the framework creates a
 * simple function for registering HTML5-ready sidebars instead of the default WordPress unordered lists.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Wrapper function for WordPress' register_sidebar() function. This function exists so that theme authors
 * can more quickly register sidebars with an HTML5 structure instead of having to write the same code
 * over and over. Theme authors are also expected to pass in the ID, name, and description of the sidebar.
 * This function can handle the rest at that point.
 *
 * @since  0.2.0
 * @access public
 * @param  array   $args
 * @return string  Sidebar ID.
 */
function carelib_register_sidebar( $args ) {

	// Set up some default sidebar arguments.
	$defaults = array(
		'id'            => '',
		'name'          => '',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);

	// Parse the arguments.
	$args = wp_parse_args( $args, apply_filters( 'carelib_sidebar_defaults', $defaults ) );

	// Remove action.
	remove_action( 'widgets_init', '__return_false', 95 );

	// Register the sidebar.
	return register_sidebar( apply_filters( 'carelib_sidebar_args', $args ) );
}

# Compatibility for when a theme doesn't register any sidebars.
add_action( 'widgets_init', '__return_false', 95 );

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since  0.2.0
 * @access public
 * @global array   $wp_registered_sidebars
 * @param  string  $sidebar_id
 * @return string
 */
function carelib_get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	return isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : '';
}
