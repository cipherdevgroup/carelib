<?php
/**
 * Methods for handling CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Gets a post style.
 *
 * @since  0.2.0
 * @access public
 * @param  int $post_id The post ID associated with the style.
 * @return bool
 */
function carelib_get_post_style( $post_id ) {
	return get_post_meta( $post_id, carelib_get_post_style_meta_key(), true );
}

/**
 * Sets a post style.
 *
 * @since  0.2.0
 * @access public
 * @param  int    $post_id The post ID associated with the style to be set.
 * @param  string $style The style to be set.
 * @return bool
 */
function carelib_set_post_style( $post_id, $style ) {
	return update_post_meta( $post_id, carelib_get_post_style_meta_key(), $style );
}

/**
 * Deletes a post style.
 *
 * @since  0.2.0
 * @access public
 * @param  int $post_id The post ID associated with the style to be deleted.
 * @return bool
 */
function carelib_delete_post_style( $post_id ) {
	return delete_post_meta( $post_id, carelib_get_post_style_meta_key() );
}

/**
 * Checks a post if it has a specific style.
 *
 * @since  0.2.0
 * @access public
 * @param  string $style The post style to check for.
 * @param  int    $post_id The ID of the post to check.
 * @return bool
 */
function carelib_has_post_style( $style, $post_id = '' ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}
	return carelib_get_post_style( $post_id ) === $style ? true : false;
}

/**
 * Wrapper function for returning the metadata key used for objects that can use styles.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_post_style_meta_key() {
	return apply_filters( "{$GLOBALS['carelib_prefix']}_style_meta_key", 'Stylesheet' );
}
