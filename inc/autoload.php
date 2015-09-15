<?php
/**
 * Autoload all library files.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
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
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct( $dir ) {
		$this->dir = $dir;
		spl_autoload_register( array( $this, 'autoloader' ) );
		spl_autoload_register( array( $this, 'admin_autoloader' ) );
		spl_autoload_register( array( $this, 'customize_autoloader' ) );
	}

	/**
	 * Format a class' name string to facilitate autoloading.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class the name of the class to replace
	 * @param  string $prefix an optional prefix to replace in the class name
	 * @return string
	 */
	protected function format_class( $class, $prefix = '' ) {
		return strtolower( str_replace( '_', '-', str_replace( "CareLib_{$prefix}", '', $class ) ) );
	}

	/**
	 * Build a path to a file.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $path the relative path to the file to be formatted
	 * @param  string $file the slug of the file to be formatted
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
	 * Load all plugin classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function autoloader( $class ) {
		return $this->require_file(
			$this->build_file( 'inc/', $this->format_class( $class ) )
		);
	}

	/**
	 * Load all admin plugin classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function admin_autoloader( $class ) {
		if ( false === strpos( $class, 'Admin' ) ) {
			return false;
		}
		return $this->require_file(
			$this->build_file( 'admin/', $this->format_class( $class, 'Admin_' ) )
		);
	}

	/**
	 * Load all customizer plugin classes when they're instantiated.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function customize_autoloader( $class ) {
		if ( false === strpos( $class, 'Customize' ) ) {
			return false;
		}
		return $this->require_file(
			$this->build_file( 'customize/', $this->format_class( $class, 'Customize_' ) )
		);
	}

}
