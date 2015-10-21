<?php
/**
 * Methods for handling JavaScript in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

abstract class CareLib_Scripts {
	/**
	 * Library version number to append to scripts.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $version;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Script suffix to determine whether or not to load minified scripts.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $suffix;

	/**
	 * The current theme's version number.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected static $theme_version;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->version = carelib()->get_version();
		$this->prefix  = carelib()->get_prefix();
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * Helper function for getting the script/style `.min` suffix for minified files.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_suffix() {
		return $this->suffix;
	}

	/**
	 * Return the current theme's version number.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function theme_version() {
		if ( is_null( self::$theme_version ) ) {
			if ( $theme = wp_get_theme() ) {
				self::$theme_version = wp_get_theme()->get( 'Version' );
			}
		}
		return self::$theme_version;
	}

	/**
	 * Return the path to the library JavaScript directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function js_uri( $path ) {
		return carelib()->get_uri( 'js/' ) . $path;
	}
}
