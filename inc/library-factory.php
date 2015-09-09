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

class CareLib_Library_Factory extends CareLib_Factory {

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
		add_action( 'after_setup_theme', array( $this, 'build_library' ), -95 );
	}

	/**
	 * Build an array of default classes to run by default.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return array $classes the default library classes to be built on init
	 */
	protected function get_default_classes() {
		$classes = array(
			'admin-factory',
			'customize-setup-factory',
			'support-factory',
			'i18n',
			'image-grabber',
			'layouts',
			'sidebar',
			'support',
		);
		if ( ! is_admin() ) {
			$classes[] = 'attributes';
			$classes[] = 'context';
			$classes[] = 'filters';
			$classes[] = 'head';
			$classes[] = 'meta';
			$classes[] = 'public-scripts';
			$classes[] = 'public-styles';
			$classes[] = 'search-form';
			$classes[] = 'template-hierarchy';
		}

		return apply_filters( "{$this->prefix}_default_classes", $classes );
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	public function build_library() {
		foreach ( (array) $this->get_default_classes() as $class ) {
			self::get( $class )->run();
		}
	}

}
