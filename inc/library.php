<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for common theme functionality.
 *
 * @version 0.1.0
 */
class CareLib_Library {
	/**
	 * Our library version number.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	const VERSION = '0.2.0';

	/**
	 * The main library file.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private $file = false;

	/**
	 * Prefix to prevent conflicts.
	 *
	 * Used to prefix filters to make them unique.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $prefix = 'carelib';

	/**
	 * A single instance of the main library class.
	 *
	 * @since 0.2.0
	 * @var   CareLib
	 */
	private static $instance;

	/**
	 * Constructor method.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct( $file ) {
		$this->set_file( $file );
	}

	/**
	 * Method to initialize the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		CareLib_Factory::get( 'factory-global' );

		if ( is_admin() ) {
			CareLib_Factory::get( 'factory-admin' );
		} else {
			CareLib_Factory::get( 'factory-public' );
		}

		if ( is_customize_preview() ) {
			CareLib_Factory::get( 'factory-customize' );
		}
	}

	/**
	 * Whether the current request is a Customizer preview.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool
	 */
	public function is_customizer_preview() {
		return is_customize_preview();
	}

	/**
	 * Whether the current environment is WordPress.com.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool
	 */
	public function is_wpcom() {
		return apply_filters( "{$this->prefix}_is_wpcom", false );
	}

	/**
	 * Setup the root file used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 * @return CareLib
	 */
	public function set_file( $file ) {
		$this->file = $file;

		return $this;
	}

	/**
	 * Set the prefix to be used by filters throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return CareLib
	 */
	public function set_prefix( $prefix ) {
		$this->prefix = sanitize_key( $prefix );

		return $this;
	}

	/**
	 * Getter method for reading the protected version variable.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string CareLib_Library::VERSION The library's version number.
	 */
	public function get_version() {
		return self::VERSION;
	}

	/**
	 * Getter method for reading the protected version variable.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function get_file() {
		return $this->file;
	}

	/**
	 * Getter method for reading the protected prefix variable.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string CareLib_Library::$prefix The library's prefix.
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Return the main CareLib instance.
	 *
	 * Allows the main library methods to be accessed without repeatedly
	 * instantiating the class needlessly.
	 *
	 * @since 0.1.0
	 * @access public
	 * @static
	 * @return CareLib
	 */
	public static function get_instance( $file ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $file );
		}
		return self::$instance;
	}
}
