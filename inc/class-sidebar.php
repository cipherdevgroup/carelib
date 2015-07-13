<?php
/**
 * Helper functions for working with the WordPress sidebar system.
 *
 * Currently, the framework creates a simple method for registering
 * HTML5-ready sidebars instead of the default WordPress unordered lists.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Template Post Class.
 */
class CareLib_Sidebar {

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
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function wp_hooks() {
		add_action( 'widgets_init', '__return_false', 95 );
	}

	/**
	 * Wrapper function for WordPress' register_sidebar() function.
	 *
	 * This function exists so that theme authors can more quickly register
	 * sidebars with an HTML5 structure instead of having to write the same code
	 * over and over. Theme authors are also expected to pass in the ID, name,
	 * and description of the sidebar.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $args
	 * @return string  Sidebar ID.
	 */
	public function register( $args ) {
		$defaults = apply_filters( "{$this->prefix}_sidebar_defaults", array(
			'id'            => '',
			'name'          => '',
			'description'   => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		$args = wp_parse_args( $args, $defaults );

		remove_action( 'widgets_init', '__return_false', 95 );

		// Register the sidebar.
		return register_sidebar( apply_filters( "{$this->prefix}_sidebar_args", $args ) );
	}

	/**
	 * Return the name of a given dynamic sidebar.
	 *
	 * @since  0.2.0
	 * @access public
	 * @global array   $wp_registered_sidebars
	 * @param  string  $sidebar_id
	 * @return string
	 */
	public function get_name( $sidebar_id ) {
		global $wp_registered_sidebars;

		return isset( $wp_registered_sidebars[ $sidebar_id ] ) ? $wp_registered_sidebars[ $sidebar_id ]['name'] : false;
	}

	/**
	 * Get a specified sidebar template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function template( $name = null ) {
		do_action( 'get_sidebar', $name ); // Core WordPress hook
		$templates = array();
		if ( ! empty( $name ) ) {
			$templates[] = "sidebar-{$name}.php";
			$templates[] = "sidebar/{$name}.php";
		}
		$templates[] = 'sidebar.php';
		$templates[] = 'sidebar/sidebar.php';
		locate_template( $templates, true );
	}

}
