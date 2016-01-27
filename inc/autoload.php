<?php
/**
 * Autoload all library files.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

defined( 'ABSPATH' ) || exit;

class CareLib_Autoload {
	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $dir;

	/**
	 * Set up properties and register the autoloaders.
	 *
	 * @since 0.2.0
	 * @param string $file The absolute path to the library's root file.
	 */
	public function __construct( $file ) {
		$this->dir = trailingslashit( dirname( $file ) );
		spl_autoload_register( array( $this, 'autoloader' ) );
	}

	/**
	 * Format a class' name string to facilitate autoloading.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class the name of the class to replace.
	 * @param  string $prefix an optional prefix to replace in the class name.
	 * @return string
	 */
	protected function format_class( $class, $prefix = '' ) {
		return str_replace( '_', '-', str_replace( "carelib_{$prefix}", '', $class ) );
	}

	/**
	 * Build a path to a file.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $path the relative path to the file to be formatted.
	 * @param  string $file the slug of the file to be formatted.
	 * @return string the formatted path to a file
	 */
	protected function build_file( $path, $file ) {
		return "{$this->dir}{$path}{$file}.php";
	}

	/**
	 * Require a file if it exists.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $file the absolute path of the file to be loaded.
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function require_file( $file ) {
		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}
		return false;
	}

	/**
	 * Load all library classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class The name of the class to be autoloaded.
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function autoloader( $class ) {
		$class  = strtolower( $class );
		$loaded = false;

		if ( false === strpos( $class, 'carelib' ) ) {
			return $loaded;
		}

		$loaded = $this->require_includes( $class );

		if ( false !== strpos( $class, 'admin' ) ) {

			$loaded = $this->require_admin( $class );

		} elseif ( false !== strpos( $class, 'customize' ) ) {

			$loaded = $this->require_customize( $class );

		}

		return $loaded;
	}

	/**
	 * Load all admin library classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class The name of the class to be autoloaded.
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function require_admin( $class ) {
		return $this->require_file( $this->build_file(
			'admin/',
			$this->format_class( $class, 'admin_' )
		) );
	}

	/**
	 * Load all customizer library classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class The name of the class to be autoloaded.
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function require_customize( $class ) {
		return $this->require_file( $this->build_file(
			'customize/',
			$this->format_class( $class, 'customize_' )
		) );
	}

	/**
	 * Load all admin library classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class The name of the class to be autoloaded.
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function require_includes( $class ) {
		return $this->require_file( $this->build_file(
			'inc/',
			$this->format_class( $class )
		) );
	}
}
