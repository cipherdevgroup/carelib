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
