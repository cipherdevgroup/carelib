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

/**
 * CareLib Attributes class.
 */
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
	public function __construct( CareLib $library ) {
		$this->dir = $library->get_dir();
		spl_autoload_register( array( $this, 'autoloader' ) );
		spl_autoload_register( array( $this, 'admin_autoloader' ) );
		spl_autoload_register( array( $this, 'customize_autoloader' ) );
	}

	/**
	 * Load all plugin classes when they're instantiated.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function replace_class( $class ) {
		return strtolower( str_replace( '_', '-', str_replace( 'CareLib_', '', $class ) ) );
	}

	protected function build_file( $path, $class ) {
		return "{$this->dir}{$path}{$class}.php";
	}

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
	 * @since  0.1.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function autoloader( $class ) {
		return $this->require_file(
			$this->build_file( 'inc/class-', $this->replace_class( $class ) )
		);
	}

	/**
	 * Load all admin plugin classes when they're instantiated.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function admin_autoloader( $class ) {
		if ( false === strpos( $class, 'Admin' ) ) {
			return false;
		}
		return $this->require_file(
			$this->build_file(
				'admin/class-',
				str_replace( 'admin-', '', $this->replace_class( $class ) )
			)
		);
	}

	/**
	 * Load all admin plugin classes when they're instantiated.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return bool true if a file is loaded, false otherwise
	 */
	protected function customize_autoloader( $class ) {
		if ( false === strpos( $class, 'Customize' ) ) {
			return false;
		}
		return $this->require_file(
			$this->build_file(
				'customize/',
				str_replace( 'customize-', '', $this->replace_class( $class ) )
			)
		);
	}

}
