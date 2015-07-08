<?php
/**
 * Media metadata factory class. This is a singleton factory class for creating
 * and storing `CareLib_Media_Meta` objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Registers and instantiates `CareLib_Media_Meta` classes.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
class CareLib_Factory_Media_Meta {

	/**
	 * Array of media meta objects created via `CareLib_Media_Meta`.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @var    array
	 */
	protected static $media = array();

	/**
	 * Gets a specific `CareLib_Media_Meta` object by post (attachment) ID.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return object
	 */
	public static function get( $post_id ) {
		// If the media meta object doesn't exist, create it.
		if ( empty( self::$media[ $post_id ] ) ) {
			self::$media[ $post_id ] = new CareLib_Media_Meta( $post_id );
		}

		return self::$media[ $post_id ];
	}

}
