<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

/**
 * Class for setting and getting all paths in the library.
 */
class CareLib_Paths {
	/**
	 * The library's directory path with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $dir = false;

	/**
	 * The library directory URL with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $uri = false;

	/**
	 * The absolute path to the parent stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $parent_dir = false;

	/**
	 * The absolute path to the child stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $child_dir = false;

	/**
	 * The URI to the parent stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $parent_uri = false;

	/**
	 * The URI to the child stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	private static $child_uri = false;

	/**
	 * Fix asset directory path on Windows installations.
	 *
	 * Because we don't know where the library is located, we need to
	 * generate a URI based on the library directory path. In order to do
	 * this, we are replacing the theme root directory portion of the
	 * library directory with the theme root URI.
	 *
	 * @since  0.2.0
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
	 * Setup the root directory path used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_dir( $file ) {
		self::$dir = trailingslashit( dirname( $file ) );
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $file the absolute path to the library's root file.
	 */
	public function set_uri( $file ) {
		self::$uri = trailingslashit( $this->normalize_uri( dirname( $file ) ) );
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function set_parent_dir() {
		if ( defined( 'PARENT_THEME_DIR' ) ) {
			self::$parent_dir = PARENT_THEME_DIR;
		} else {
			self::$parent_dir = trailingslashit( get_template_directory() );
		}
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function set_parent_uri() {
		if ( defined( 'PARENT_THEME_URI' ) ) {
			self::$parent_uri = PARENT_THEME_URI;
		} else {
			self::$parent_uri = trailingslashit( get_template_directory_uri() );
		}
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function set_child_dir() {
		if ( defined( 'CHILD_THEME_DIR' ) ) {
			self::$child_dir = CHILD_THEME_DIR;
		} else {
			self::$child_dir = trailingslashit( get_stylesheet_directory() );
		}
	}

	/**
	 * Setup the root directory URI used throughout the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function set_child_uri() {
		if ( defined( 'CHILD_THEME_URI' ) ) {
			self::$child_uri = CHILD_THEME_URI;
		} else {
			self::$child_uri = trailingslashit( get_stylesheet_directory_uri() );
		}
	}

	/**
	 * Return the path to the CareLib directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the library directory.
	 * @return string
	 */
	public function get_dir( $path = '' ) {
		if ( ! self::$dir ) {
			$this->set_dir( carelib()->get_file() );
		}

		return self::$dir . ltrim( $path );
	}

	/**
	 * Return the URI to the CareLib directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the library URI.
	 * @return string
	 */
	public function get_uri( $path = '' ) {
		if ( ! self::$uri ) {
			$this->set_uri( carelib()->get_file() );
		}

		return self::$uri . ltrim( $path );
	}

	/**
	 * Return the path to the library css directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the library CSS URI.
	 * @return string
	 */
	public function get_css_uri( $path ) {
		return $this->get_uri( 'css/' ) . ltrim( $path );
	}

	/**
	 * Return the path to the library JS directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the library JS URI.
	 * @return string
	 */
	public function get_js_uri( $path ) {
		return $this->get_uri( 'js/' ) . ltrim( $path );
	}

	/**
	 * Return the path to the parent theme directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the parent directory.
	 * @return string
	 */
	public function get_parent_dir( $path = '' ) {
		if ( ! self::$parent_dir ) {
			$this->set_parent_dir();
		}

		return self::$parent_dir . ltrim( $path );
	}

	/**
	 * Return the path to the parent theme URI with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the parent URI.
	 * @return string
	 */
	public function get_parent_uri( $path = '' ) {
		if ( ! self::$parent_uri ) {
			$this->set_parent_uri();
		}

		return self::$parent_uri . ltrim( $path );
	}

	/**
	 * Return the path to the child theme directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the child directory.
	 * @return string
	 */
	public function get_child_dir( $path = '' ) {
		if ( ! self::$child_dir ) {
			$this->set_parent_dir();
		}

		return self::$child_dir . ltrim( $path );
	}

	/**
	 * Return the path to the child theme URI with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $path An optional path to append to the child URI.
	 * @return string
	 */
	public function get_child_uri( $path = '' ) {
		if ( ! self::$child_uri ) {
			$this->set_parent_uri();
		}

		return self::$child_uri . ltrim( $path );
	}
}
