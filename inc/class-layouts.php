<?php
/**
 * Methods for interacting with `CareLib_Layout` objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Hybrid Layout class. This is the backbone of the Layouts API.
 *
 * @since  0.2.0
 * @access public
 */
class CareLib_Layouts {

	/**
	 * Array of layout objects.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    array
	 */
	protected static $layouts = array();

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
		$this->prefix = CareLib::instance()->get_prefix();
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
		add_action( 'init',                                 array( $this, 'register_layouts' ), 95 );
		add_filter( 'current_theme_supports-theme-layouts', array( $this, 'theme_layouts_support' ), 10, 3 );
		add_filter( 'theme_mod_theme_layout',               array( $this, 'filter_layout' ), 5 );
	}

	/**
	 * Register a new layout object
	 *
	 * @see    CareLib_Layout::__construct()
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_layout( $name, $args = array() ) {
		if ( ! $this->layout_exists( $name ) ) {
			$this->layouts[ $name ] = CareLib_Factory::build( 'layout', $name, $args );
		}
	}

	/**
	 * Unregisters a layout object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister_layout( $name ) {
		if ( $this->layout_exists( $name ) ) {
			unset( $this->layouts[ $name ] );
		}
	}

	/**
	 * Checks if a layout exists.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function layout_exists( $name ) {
		return isset( $this->layouts[ $name ] );
	}

	/**
	 * Gets a layout object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get_layout( $name ) {
		return $this->layout_exists( $name ) ? $this->layouts[ $name ] : false;
	}

	/**
	 * Gets all layout objects.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object
	 */
	public function get_layouts() {
		return $this->layouts;
	}

	/**
	 * Registers the default theme layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_layouts() {
		$this->register_layout(
			'default',
			array(
				// Translators: Default theme layout option.
				'label'            => esc_html_x( 'Default', 'theme layout', 'carelib' ),
				'is_global_layout' => false,
				'_builtin'         => true,
				'_internal'        => true,
			)
		);

		// Hook for registering theme layouts. Theme should always register on this hook.
		do_action( "{$this->prefix}_register_layouts" );
	}

	/**
	 * Gets the theme layout. This is the global theme layout defined. Other functions filter the
	 * available `theme_mod_theme_layout` hook to overwrite this.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_theme_layout() {
		return get_theme_mod( 'theme_layout', $this->get_default_layout() );
	}

	/**
	 * Returns the default layout defined by the theme.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_default_layout() {
		$support = get_theme_support( 'theme-layouts' );

		return isset( $support[0] ) && isset( $support[0]['default'] ) ? $support[0]['default'] : 'default';
	}

	/**
	 * Gets a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function get_post_layout( $post_id ) {
		return get_post_meta( $post_id, $this->get_meta_key(), true );
	}

	/**
	 * Sets a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @param  string  $layout
	 * @return bool
	 */
	public function set_post_layout( $post_id, $layout ) {
		return 'default' !== $layout ? update_post_meta( $post_id, $this->get_meta_key(), $layout ) : $this->delete_post_layout( $post_id );
	}

	/**
	 * Deletes a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function delete_post_layout( $post_id ) {
		return delete_post_meta( $post_id, $this->get_meta_key() );
	}

	/**
	 * Checks a post if it has a specific layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function has_post_layout( $layout, $post_id = '' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		return $this->get_post_layout( $post_id ) === $layout ? true : false;
	}

	/**
	 * Gets a user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $user_id
	 * @return bool
	 */
	public function get_user_layout( $user_id ) {
		return get_user_meta( $user_id, $this->get_meta_key(), true );
	}

	/**
	 * Sets a user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $user_id
	 * @param  string  $layout
	 * @return bool
	 */
	public function set_user_layout( $user_id, $layout ) {
		return 'default' !== $layout ? update_user_meta( $user_id, $this->get_meta_key(), $layout ) : $this->delete_user_layout( $user_id );
	}

	/**
	 * Deletes user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $user_id
	 * @return bool
	 */
	public function delete_user_layout( $user_id ) {
		return delete_user_meta( $user_id, $this->get_meta_key() );
	}

	/**
	 * Checks if a user/author has a specific layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $layout
	 * @param  int     $user_id
	 * @return bool
	 */
	public function has_user_layout( $layout, $user_id = '' ) {
		if ( ! $user_id ) {
			$user_id = absint( get_query_var( 'author' ) );
		}

		return $this->get_user_layout( $user_id ) === $layout ? true : false;
	}

	/**
	 * Default filter on the `theme_mod_theme_layout` hook. By default, we'll check for per-post
	 * or per-author layouts saved as metadata. If set, we'll filter. Else, just return the
	 * global layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $theme_layout
	 * @return string
	 */
	public function filter_layout( $theme_layout ) {
		// If viewing a singular post, get the post layout.
		if ( is_singular() ) {
			$layout = $this->get_post_layout( get_queried_object_id() );
		} elseif ( is_author() ) {
			// If viewing an author archive, get the user layout.
			$layout = $this->get_user_layout( get_queried_object_id() );
		}

		return ! empty( $layout ) && 'default' !== $layout ? $layout : $theme_layout;
	}

	/**
	 * Returns an array of the available theme layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  bool   $supports
	 * @param  array  $args
	 * @param  array  $feature
	 * @return bool
	 */
	public function theme_layouts_support( $supports, $args, $feature ) {
		if ( ! isset( $args[0] ) || ! in_array( $args[0], array( 'customize', 'post_meta' ) ) ) {
			return $supports;
		}

		if ( is_array( $feature[0] ) && isset( $feature[0][ $args[0] ] ) && false === $feature[0][ $args[0] ] ) {
			$supports = false;
		}

		return $supports;
	}

	/**
	 * Wrapper function for returning the metadata key used for objects that can use layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_meta_key() {
		return apply_filters( "{$this->prefix}_layout_meta_key", 'Layout' );
	}

}
