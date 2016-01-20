<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

// Exit if accessed directly
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
	const VERSION = '0.1.0';

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
	 * The main library file.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private static $file = false;

	/**
	 * The library's directory path with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private static $dir = false;

	/**
	 * The library directory URL with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private static $uri = false;

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
	 * Fix asset directory path on Windows installations.
	 *
	 * Because we don't know where the library is located, we need to
	 * generate a URI based on the library directory path. In order to do
	 * this, we are replacing the theme root directory portion of the
	 * library directory with the theme root URI.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  string $path the absolute path to the library's root directory.
	 * @uses   trailingslashit()
	 * @uses   get_template_directory()
	 * @uses   get_theme_root_uri()
	 * @return string a normalized uri string.
	 */
	protected function normalize_uri( $path ) {
		return trailingslashit( get_theme_root_uri() ) . strstr( wp_normalize_path( $path ), basename( get_template_directory() ) );
	}

	/**
	 * Setup the root file used throughout the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_file( $file ) {
		self::$file = $file;
	}

	/**
	 * Setup the root directory path used throughout the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_dir( $file ) {
		self::$dir = trailingslashit( dirname( $file ) );
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_uri( $file ) {
		self::$uri = trailingslashit( $this->normalize_uri( dirname( $file ) ) );
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
	 * Method to initialize the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		CareLib_Factory::get( 'global-factory' );

		if ( is_admin() ) {
			CareLib_Factory::get( 'admin-factory' );
		} else {
			CareLib_Factory::get( 'public-factory' );
		}

		if ( is_customize_preview() ) {
			CareLib_Factory::get( 'customize-setup-factory' );
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
	 * Getter method for reading the protected version variable.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function get_version() {
		return self::VERSION;
	}

	/**
	 * Getter method for reading the protected prefix variable.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Return the path to the CareLib directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_dir( $path = '' ) {
		if ( ! self::$dir ) {
			$this->set_dir( self::$file );
		}

		return self::$dir . ltrim( $path );
	}

	/**
	 * Return the URI to the CareLib directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_uri( $path = '' ) {
		if ( ! self::$uri ) {
			$this->set_uri( self::$file );
		}

		return self::$uri . ltrim( $path );
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
