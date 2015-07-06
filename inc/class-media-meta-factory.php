<?php
/**
 * Media metadata factory class. This is a singleton factory class for creating and storing
 * `CareLib_Media_Meta` objects.
 *
 * Theme authors need not access this class directly. Instead, utilize the template tags in the
 * `/inc/template-media.php` file.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Singleton factory class that registers and instantiates `CareLib_Media_Meta` classes. Use the
 * `carelib_media_factory()` function to get the instance.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
class CareLib_Media_Meta_Factory {

	/**
	 * Array of media meta objects created via `CareLib_Media_Meta`.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @var    array
	 */
	protected $media = array();

	/**
	 * Creates a new `CareLib_Media_Meta` object and stores it in the `$media` array by
	 * post ID.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int       $post_id
	 */
	protected function create_media_meta( $post_id ) {
		$this->media[ $post_id ] = new CareLib_Media_Meta( $post_id );
	}

	/**
	 * Gets a specific `CareLib_Media_Meta` object by post (attachment) ID.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return object
	 */
	public function get_media_meta( $post_id ) {
		// If the media meta object doesn't exist, create it.
		if ( ! isset( $this->media[ $post_id ] ) ) {
			$this->create_media_meta( $post_id );
		}

		return $this->media[ $post_id ];
	}

	/**
	 * Returns the instance of the `CareLib_Media_Meta_Factory`.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new CareLib_Media_Meta_Factory;
		}
		return $instance;
	}

}
