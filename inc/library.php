<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
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
class CareLib {

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
	 * @type  string
	 */
	private $prefix = 'carelib';

	/**
	 * The main library file.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $file = false;

	/**
	 * The library's directory path with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $dir = false;

	/**
	 * The library directory URL with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $uri = false;

	/**
	 * A single instance of the main library class.
	 *
	 * @since 0.2.0
	 * @var   CareLib
	 */
	private static $instance;

	/**
	 * Setup all paths used throughout the library.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_paths( $file ) {
		$this->file = $file;
		$this->dir  = trailingslashit( dirname( $file ) );
		$this->uri  = trailingslashit( $this->normalize_uri( dirname( $file ) ) );
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
			self::$instance = new self();
			self::$instance->set_paths( $file );
		}
		return self::$instance;
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
		if ( $this->file && $this->dir && $this->uri ) {
			$this->autoload();
			$this->build();
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
		return $this->dir . ltrim( $path );
	}

	/**
	 * Return the URI to the CareLib directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_uri( $path = '' ) {
		return $this->uri . ltrim( $path );
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
	 * @uses   get_theme_root()
	 * @uses   get_theme_root_uri()
	 * @return string a normalized uri string
	 */
	protected function normalize_uri( $path ) {
		return str_replace(
			wp_normalize_path( get_theme_root() ),
			get_theme_root_uri(),
			wp_normalize_path( $path )
		);
	}

	/**
	 * Load all plugin classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function autoload() {
		new CareLib_Autoload( $this->dir );
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	protected function build() {
		CareLib_Factory::get( 'library-factory' )->run();
	}

}
