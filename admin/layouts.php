<?php
/**
 * Methods for interacting with `CareLib_Layout` objects within the admin panel.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Check if the current layout has a metabox of any kind.
 *
 * @since  0.1.0
 * @access public
 * @param  CareLib_Layout $layout The layout object to check.
 * @param  string         $post_type The post type of the current post.
 * @return bool True if the layout has a metabox, false otherwise.
 */
function carelib_layout_has_meta_box( CareLib_Layout $layout, $post_type ) {
	if ( ! $layout->get_image() ) {
		return false;
	}

	if ( ! carelib_post_type_has_layout( $post_type, $layout ) ) {
		return false;
	}

	return true;
}

/**
 * Check if the current layout has a post metabox.
 *
 * @since  0.1.0
 * @access public
 * @param  CareLib_Layout $layout The layout object to check.
 * @param  string         $post_type The post type of the current post.
 * @return bool True if the layout has a post metabox, false otherwise.
 */
function carelib_layout_has_post_metabox( CareLib_Layout $layout, $post_type ) {
	if ( true !== $layout->is_post() ) {
		return false;
	}

	return carelib_layout_has_meta_box( $layout, $post_type );
}

/**
 * Disable the layout meta box if the current page uses a forced layout.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $post_type The post type of the current post.
 * @param  WP_Post $post The post type object of the current post.
 * @return void
 */
function carelib_maybe_disable_post_layout_metabox( $post_type, $post ) {
	if ( $post instanceof WP_Post && _carelib_admin_is_layout_forced( $post_type, $post->ID ) ) {
		add_filter( "{$GLOBALS['carelib_prefix']}_allow_layout_control", '__return_false' );
	}
}


/**
 * Disable the layout meta box if the current page uses a forced layout.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $post_type The post type of the current post.
 * @param  WP_Post $post The post type object of the current post.
 * @return bool
 */
function _carelib_admin_is_layout_forced( $post_type, $post_id ) {
	if ( _carelib_is_post_type_layout_forced( $post_type ) ) {
		return true;
	}

	if ( _carelib_is_post_layout_forced( $post_id ) ) {
		return true;
	}

	if ( _carelib_is_template_layout_forced( $post_id ) ) {
		return true;
	}

	return carelib_is_layout_forced();
}
