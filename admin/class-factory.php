<?php
/**
 * Build all the default classes necessary for the library to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Factory extends CareLib_Factory {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Method to fire all actions within the class.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		if ( is_admin() ) {
			add_action( 'after_setup_theme', array( $this, 'build_admin' ), -90 );
		}
	}

	/**
	 * Build an array of default classes to run by default.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return array $classes the default library classes to be built on init
	 */
	protected function get_admin_classes() {
		return apply_filters( "{$this->prefix}_admin_classes", array(
			'metabox-post-layouts',
			'metabox-post-styles',
			'metabox-post-templates',
			'scripts',
			'tinymce',
		) );
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	public function build_admin() {
		foreach ( (array) $this->get_admin_classes() as $class ) {
			self::get( "admin-{$class}" )->run();
		}
	}

}
