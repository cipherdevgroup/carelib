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

class CareLib_Builder {

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
		add_action( 'after_setup_theme', array( $this, 'build' ),         -95 );
		add_action( 'after_setup_theme', array( $this, 'theme_support' ),  25 );
	}

	/**
	 * Build an array of default classes to run by default.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return array $classes the default library classes to be built on init
	 */
	protected function get_default_classes() {
		$classes = array(
			'customize',
			'i18n',
			'image-grabber',
			'layouts',
			'sidebar',
		);
		if ( is_admin() ) {
			$classes[] = 'admin-metabox-post-layouts';
			$classes[] = 'admin-metabox-post-styles';
			$classes[] = 'admin-metabox-post-templates';
			$classes[] = 'admin-scripts';
			$classes[] = 'admin-tinymce';
		} else {
			$classes[] = 'attributes';
			$classes[] = 'context';
			$classes[] = 'filters';
			$classes[] = 'head';
			$classes[] = 'meta';
			$classes[] = 'public-scripts';
			$classes[] = 'search-form';
			$classes[] = 'support';
			$classes[] = 'template-hierarchy';
		}

		return apply_filters( "{$this->prefix}_default_classes", $classes );
	}

	/**
	 * Add conditional classes based on theme support.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  array $classes the existing default library classes
	 * @return array $classes the modified classes based on theme support
	 */
	protected function get_conditional_classes( $classes ) {
		if ( current_theme_supports( 'theme-layouts' ) ) {
			$classes[] = 'layouts';
		}
		if ( is_admin() ) {
			if ( current_theme_supports( 'theme-dashboard' ) ) {
				$classes[] = 'admin-dashboard';
			}
		} else {
			if ( current_theme_supports( 'site-logo' ) && ! function_exists( 'jetpack_the_site_logo' ) ) {
				$classes[] = 'site-logo';
			}
		}

		return $classes;
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	public function build() {
		foreach ( (array) $this->get_default_classes() as $class ) {
			$object = CareLib_Factory::get( $class );
			$object->run();
		}
	}

	/**
	 * Loads and instantiates all functionality which requires theme support.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function theme_support() {
		add_filter( "{$this->prefix}_default_classes", array( $this, 'get_conditional_classes' ) );
	}

}
