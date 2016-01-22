<?php
/**
 * Methods for handling CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

abstract class CareLib_Styles extends CareLib_Scripts {
	/**
	 * Gets a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function get_post_style( $post_id ) {
		return get_post_meta( $post_id, $this->get_style_meta_key(), true );
	}

	/**
	 * Sets a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @param  string  $layout
	 * @return bool
	 */
	public function set_post_style( $post_id, $style ) {
		return update_post_meta( $post_id, $this->get_style_meta_key(), $style );
	}

	/**
	 * Deletes a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function delete_post_style( $post_id ) {
		return delete_post_meta( $post_id, $this->get_style_meta_key() );
	}

	/**
	 * Checks a post if it has a specific style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function has_post_style( $style, $post_id = '' ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		return $this->get_post_style( $post_id ) === $style ? true : false;
	}

	/**
	 * Wrapper function for returning the metadata key used for objects that can use styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_style_meta_key() {
		return apply_filters( "{$this->prefix}_style_meta_key", 'Stylesheet' );
	}
}
