<?php
/**
 * Build and store references to our plugin objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Factory {

	protected static $objects = array();

	/**
	 * Build a named object and return it. Keep a reference when building
	 * so we can reuse it later.
	 *
	 * If you pass 'my-object' to $object, the Factory will instantiate
	 * 'CareLib_My_Object'.
	 *
	 * @param  string $object Object name.
	 * @param  string $name Optional. Name of the object.
	 * @throws InvalidArgumentException If the specified class does not exist.
	 * @return mixed
	 */
	public static function build( $object, $name = 'canonical', $args = array() ) {
		if ( empty( self::$objects[ $object ] ) ) {
			self::$objects[ $object ] = array();
		}

		$class_name = 'CareLib_' . ucwords( str_replace( '-', '_', $object ) );

		if ( ! class_exists( $class_name ) ) {
			throw new InvalidArgumentException(
				"No class exists for the '{$object}' object."
			);
		}

		if ( empty( self::$objects[ $object ][ $name ] ) ) {
			self::$objects[ $object ][ $name ] = new $class_name( $args );
		}

		return self::$objects[ $object ][ $name ];
	}

	/**
	 * Get the saved instance of a specified object.
	 *
	 * @param  string $object Object name.
	 * @param  string $name Optional. Name of the object.
	 * @return mixed
	 */
	public static function get( $object, $name = 'canonical' ) {
		return self::$objects[ $object ][ $name ];
	}

}
