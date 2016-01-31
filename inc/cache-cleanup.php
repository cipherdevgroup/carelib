<?php
/**
 * Methods for handling the cleanup of object cache throughout the framework.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Delete the image cache.
 *
 * @since  0.2.0
 * @access protected
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function _carelib_delete_image_cache( $post_id ) {
	return wp_cache_delete( $post_id, "{$GLOBALS['carelib_prefix']}_image_grabber" );
}

/**
 * Delete the image cache for the specific post when the 'save_post' hook
 * is fired.
 *
 * @since  0.2.0
 * @access protected
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function carelib_delete_image_cache_by_post( $post_id ) {
	return _carelib_delete_image_cache( $post_id );
}

/**
 * Delete the image cache for a specific post when the 'added_post_meta',
 * 'deleted_post_meta', or 'updated_post_meta' hooks are called.
 *
 * @since  0.2.0
 * @access protected
 * @param  int $meta_id The ID of the metadata being updated.
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function carelib_delete_image_cache_by_meta( $meta_id, $post_id ) {
	return _carelib_delete_image_cache( $post_id );
}
