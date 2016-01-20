<?php
/**
 * Methods for interacting with `CareLib_Layout` objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

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
	 * The name of the default layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    array
	 */
	protected static $default = 'default';

	/**
	 * Whether or not the current theme has enabled support for layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    array
	 */
	protected static $has_support = false;

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
	public function add_support() {
		self::$has_support = true;

		$this->wp_hooks();

		if ( is_customize_preview() ) {
			carelib_get( 'customize-setup-register' )->add_layouts_support();
		}

		if ( is_admin( ) ) {
			carelib_get( 'admin-metabox-post-layouts' )->add_layouts_support();
		}

		return $this;
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'init',                             array( $this, 'register_layouts' ), 95 );
		add_filter( "{$this->prefix}_get_theme_layout", array( $this, 'filter_layout' ), 5 );
	}

	/**
	 * Check if the current theme has layouts support.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function has_support() {
		return (bool) self::$has_support;
	}

	/**
	 * Check if a layout exists.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function layout_exists( $name ) {
		return isset( self::$layouts[ $name ] );
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
			self::$layouts[ $name ] = new CareLib_Layout( $name, $args );
		}
	}

	/**
	 * Register the default theme layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_layouts() {
		if ( ! self::$has_support ) {
			return false;
		}

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

		return true;
	}

	/**
	 * Set the default layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function set_default( $value ) {
		self::$default = (string) $value;

		return $this;
	}

	/**
	 * Default filter on the `theme_mod_theme_layout` hook.
	 *
	 * By default, we'll check for per-post or per-author layouts saved as
	 * metadata. If set, we'll filter. Else, just return the global layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $theme_layout
	 * @return string
	 */
	public function filter_layout( $theme_layout ) {
		if ( is_singular() ) {
			$layout = $this->get_post_layout( get_queried_object_id() );
		}
		if ( is_author() ) {
			$layout = $this->get_user_layout( get_queried_object_id() );
		}

		return ! empty( $layout ) && 'default' !== $layout ? $layout : $theme_layout;
	}

	/**
	 * Wrapper function for returning the metadata key used for objects that can
	 * use layouts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_meta_key() {
		return apply_filters( "{$this->prefix}_layout_meta_key", 'Layout' );
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
	 * Get all layout objects.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object
	 */
	public function get_layouts() {
		return self::$layouts;
	}

	/**
	 * Get a layout object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $name
	 * @return object|bool
	 */
	public function get_layout( $name ) {
		return $this->layout_exists( $name ) ? self::$layouts[ $name ] : false;
	}

	/**
	 * Get the theme layout.
	 *
	 * This is the global theme layout defined. Other functions filter the
	 * available `theme_mod_theme_layout` hook to overwrite this.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_theme_layout() {
		return apply_filters( "{$this->prefix}_get_theme_layout", $this->get_global_layout() );
	}

	/**
	 * Returns the theme mod used for the global layout setting.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_global_layout() {
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
		return self::$default;
	}

	/**
	 * Determines whether or not a user should be able to control the layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function allow_layout_control() {
		return apply_filters( "{$this->prefix}_allow_layout_control", true );
	}

	/**
	 * Checks a post if it has a specific layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @return bool
	 */
	public function has_post_layout( $layout, $post_id = '' ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

		return $this->get_post_layout( $post_id ) === $layout ? true : false;
	}

	/**
	 * Checks if a user/author has a specific layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $layout
	 * @param  int $user_id
	 * @return bool
	 */
	public function has_user_layout( $layout, $user_id = '' ) {
		$user_id = empty( $user_id ) ? absint( get_query_var( 'author' ) ) : $user_id;

		return $this->get_user_layout( $user_id ) === $layout ? true : false;
	}

	/**
	 * Sets a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @param  string $layout
	 * @return bool
	 */
	public function set_post_layout( $post_id, $layout ) {
		if ( 'default' !== $layout ) {
			return update_post_meta( $post_id, $this->get_meta_key(), $layout );
		}
		return $this->delete_post_layout( $post_id );
	}

	/**
	 * Sets a user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $user_id
	 * @param  string $layout
	 * @return bool
	 */
	public function set_user_layout( $user_id, $layout ) {
		if ( 'default' !== $layout ) {
			return update_user_meta( $user_id, $this->get_meta_key(), $layout );
		}
		return $this->delete_user_layout( $user_id );
	}

	/**
	 * Unregisters a layout object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $name
	 * @return void
	 */
	public function unregister_layout( $name ) {
		if ( $this->layout_exists( $name ) ) {
			unset( self::$layouts[ $name ] );
		}
	}

	/**
	 * Deletes a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @return bool
	 */
	public function delete_post_layout( $post_id ) {
		return delete_post_meta( $post_id, $this->get_meta_key() );
	}

	/**
	 * Deletes user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $user_id
	 * @return bool
	 */
	public function delete_user_layout( $user_id ) {
		return delete_user_meta( $user_id, $this->get_meta_key() );
	}
}
