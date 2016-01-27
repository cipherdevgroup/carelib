<?php
/**
 * Build and store references to our library objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Factory {
	/**
	 * A list of required library object names.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected $required = array();

	/**
	 * The saved library objects.
	 *
	 * @since 0.1.0
	 * @type object
	 */
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
	 * @param  array $args arguments to be passed to the class object
	 * @throws InvalidArgumentException If the specified class does not exist.
	 * @return mixed
	 */
	public static function build( $object, $name = 'canonical', $args = array() ) {
		if ( empty( self::$objects[ $object ] ) ) {
			self::$objects[ $object ] = array();
		}

		$class = str_replace( '-', '_', $object );
		$class = "carelib_{$class}";

		if ( ! class_exists( $class ) ) {
			throw new InvalidArgumentException(
				"No class exists for the '{$object}' object."
			);
		}

		if ( empty( self::$objects[ $object ][ $name ] ) ) {
			self::$objects[ $object ][ $name ] = new $class( $args );
		}

		return self::$objects[ $object ][ $name ];
	}

	/**
	 * Get the saved instance of a specified object.
	 *
	 * @param  string $object Object name.
	 * @param  string $name Optional. Name of the object.
	 * @param  array $args arguments to be passed to the class object
	 * @return mixed
	 */
	public static function get( $object, $name = 'canonical', $args = array() ) {
		if ( isset( self::$objects[ $object ][ $name ] ) ) {
			return self::$objects[ $object ][ $name ];
		}
		return self::build( $object, $name, $args );
	}

	/**
	 * Run and store a reference to objects which are required for the plugin
	 * to operate.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	public function build_required_objects() {
		if ( empty( $this->required ) ) {
			throw new InvalidArgumentException(
				'No required objects have been defined.'
			);
		}
		foreach ( $this->required as $class ) {
			$object = self::get( $class );
			$object->run();
		}
	}
}
