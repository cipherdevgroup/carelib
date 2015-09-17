<?php
/**
 * Methods for handling the cleanup of object cache throughout the framework.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Cache_Cleanup {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'save_post',         array( $this, 'delete_image_cache_by_post' ), 10 );
		add_action( 'deleted_post_meta', array( $this, 'delete_image_cache_by_meta' ), 10, 2 );
		add_action( 'updated_post_meta', array( $this, 'delete_image_cache_by_meta' ), 10, 2 );
		add_action( 'added_post_meta',   array( $this, 'delete_image_cache_by_meta' ), 10, 2 );
	}

	/**
	 * Delete the image cache.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int $post_id The ID of the post to delete the cache for.
	 * @return bool true when cache is deleted, false otherwise
	 */
	protected function delete_image_cache( $post_id ) {
		return wp_cache_delete( $post_id, "{$this->prefix}_image_grabber" );
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
	public function delete_image_cache_by_post( $post_id ) {
		return $this->delete_image_cache( $post_id );
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
	public function delete_image_cache_by_meta( $meta_id, $post_id ) {
		return $this->delete_image_cache( $post_id );
	}

}
