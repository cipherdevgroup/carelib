<?php
/**
 * Metadata functions used in the core framework.  This file registers meta keys for use in WordPress
 * in a safe manner by setting up a custom sanitize callback.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

# Register meta on the 'init' hook.
add_action( 'init', 'carelib_register_meta', 15 );

/**
 * Registers the framework's custom metadata keys and sets up the sanitize callback function.
 *
 * @since  1.3.0
 * @access public
 * @return void
 */
function carelib_register_meta() {
	// Register meta if the theme supports the 'hybrid-core-template-hierarchy' feature.
	if ( current_theme_supports( 'hybrid-core-template-hierarchy' ) ) {

		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			if ( 'page' !== $post_type ) {
				register_meta(
					'post',
					"_wp_{$post_type}_template",
					'sanitize_text_field',
					'__return_false'
				);
			}
		}
	}

	// Theme layouts meta.
	if ( current_theme_supports( 'theme-layouts' ) ) {
		register_meta( 'post', carelib_get_layout_meta_key(), 'sanitize_key', '__return_false' );
		register_meta( 'user', carelib_get_layout_meta_key(), 'sanitize_key', '__return_false' );
	}

	// Post styles meta.
	register_meta( 'post', carelib_get_style_meta_key(), 'sanitize_text_field', '__return_false' );
}
