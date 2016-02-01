<?php
/**
 * Metadata functions used in the core library.
 *
 * This file registers meta keys for use in WordPress in a safe manner by
 * setting up a custom sanitize callback.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Registers the library's custom metadata keys and sets up the sanitize
 * callback function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_register_post_template_meta() {
	foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {

		if ( 'page' === $post_type ) {
			continue;
		}

		register_meta(
			'post',
			"_wp_{$post_type}_template",
			'sanitize_text_field',
			'__return_false'
		);
	}
}

/**
 * Registers the library's custom metadata keys and sets up the sanitize
 * callback function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_register_layouts_meta() {
	register_meta(
		'post',
		carelib_get_layout_meta_key(),
		'sanitize_key',
		'__return_false'
	);
	register_meta(
		'user',
		carelib_get_layout_meta_key(),
		'sanitize_key',
		'__return_false'
	);
}

/**
 * Registers the library's custom metadata keys and sets up the sanitize
 * callback function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_register_post_style_meta() {
	register_meta(
		'post',
		carelib_get_post_style_meta_key(),
		'sanitize_text_field',
		'__return_false'
	);
}
