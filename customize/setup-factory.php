<?php
/**
 * Build all the default classes necessary for the customizer features to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Customize_Setup_Factory extends CareLib_Factory {

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
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		add_action( 'after_setup_theme', array( $this, 'build_objects' ) );
	}

	/**
	 * Build an array of default classes to run by default.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return array $classes the default library classes to be built on init
	 */
	protected function get_classes() {
		$classes = array();

		if ( is_customize_preview() ) {
			$classes[] = 'register';
			$classes[] = 'scripts';
			$classes[] = 'settings';
			$classes[] = 'styles';
		}

		return apply_filters( "{$this->prefix}_customize_setup_classes", $classes );
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	public function build_objects() {
		foreach ( (array) $this->get_classes() as $class ) {
			self::get( "customize-setup-{$class}" )->run();
		}
	}
}
