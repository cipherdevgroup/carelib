<?php
/**
 * Methods for interacting with `CareLib_Layout` objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Get all layout objects.
 *
 * @since  1.0.0
 * @access public
 * @global array $_carelib_layouts Holds all layouts data.
 * @return object
 */
function carelib_get_layouts() {
	global $_carelib_layouts;

	if ( ! isset( $_carelib_layouts ) ) {
		$_carelib_layouts = array();
	}

	return (array) $_carelib_layouts;
}

/**
 * Check if the current theme has layouts.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function carelib_has_layouts() {
	$layouts = carelib_get_layouts();

	return ! empty( $layouts );
}

/**
 * Check if a layout exists.
 *
 * @since  1.0.0
 * @access public
 * @param  string $layout_id The ID of the layout to check.
 * @return bool
 */
function carelib_layout_exists( $layout_id ) {
	$layouts = carelib_get_layouts();

	return isset( $layouts[ $layout_id ] );
}

/**
 * Register a new layout object.
 *
 * @see    CareLib_Layout::__construct()
 * @since  1.0.0
 * @access public
 * @param  string $layout_id The ID of the layout to be registered.
 * @param  array  $args The properties of the layout to be registered.
 * @return void
 */
function carelib_register_layout( $layout_id, $args = array() ) {
	global $_carelib_layouts;

	if ( ! isset( $_carelib_layouts ) ) {
		$_carelib_layouts = array();
	}

	if ( ! carelib_layout_exists( $layout_id ) ) {
		$_carelib_layouts[ $layout_id ] = new CareLib_Layout( $layout_id, $args );
	}
}

/**
 * Register multiple layout objects.
 *
 * @since  1.0.0
 * @access public
 * @param  array $layouts A list of layouts and their associated properties.
 * @return void
 */
function carelib_register_layouts( $layouts ) {
	foreach ( (array) $layouts as $layout_id => $args ) {
		carelib_register_layout( $layout_id, $args );
	}
}

/**
 * Register the default theme layouts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_do_register_layouts() {
	do_action( "{$GLOBALS['carelib_prefix']}_register_layouts" );
}

/**
 * Set a default layout.
 *
 * Allow a user to identify a layout as being the default layout on a new
 * install, as well as serve as the fallback layout.
 *
 * @since  1.0.0
 * @param  string $name Name of layout to set as default.
 * @return boolean|string False if layout is not registered. ID otherwise.
 */
function carelib_set_default_layout( $name ) {
	carelib_register_layout(
		'default',
		array(
			// Translators: Default theme layout option.
			'label'            => esc_html_x( 'Default', 'theme layout', 'carelib' ),
			'is_global_layout' => false,
			'_builtin'         => true,
			'_internal'        => true,
		)
	);

	$layouts = carelib_get_layouts();

	// Don't allow unregistered layouts.
	if ( ! isset( $layouts[ $name ] ) ) {
		return false;
	}

	$layouts['default']->set_name( $name );

	return true;
}

/**
 * Default filter on the `theme_mod_theme_layout` hook.
 *
 * By default, we'll check for per-post or per-author layouts saved as
 * metadata. If set, we'll filter. Else, just return the global layout.
 *
 * @since  1.0.0
 * @access public
 * @param  string $theme_layout The current global theme layout.
 * @return string The modified theme layout based on which page is viewed.
 */
function carelib_filter_layout( $theme_layout ) {
	if ( is_singular() ) {
		$layout = carelib_get_post_layout( get_queried_object_id() );
	} elseif ( is_author() ) {
		$layout = carelib_get_user_layout( get_queried_object_id() );
	} elseif ( carelib_is_blog_archive() ) {
		$layout = carelib_get_post_layout( get_option( 'page_for_posts' ) );
	}

	return ! empty( $layout ) && 'default' !== $layout ? $layout : $theme_layout;
}

/**
 * Wrapper function for returning the metadata key used for objects that can
 * use layouts.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_layout_meta_key() {
	return apply_filters( "{$GLOBALS['carelib_prefix']}_layout_meta_key", 'Layout' );
}

/**
 * Gets a post layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $post_id The ID of the post from which the layout will be retrieved.
 * @return bool
 */
function carelib_get_post_layout( $post_id ) {
	return get_post_meta( $post_id, carelib_get_layout_meta_key(), true );
}

/**
 * Gets a user layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $user_id The ID of the user from which the layout will be retrieved.
 * @return bool
 */
function carelib_get_user_layout( $user_id ) {
	return get_user_meta( $user_id, carelib_get_layout_meta_key(), true );
}

/**
 * Get a layout object.
 *
 * @since  1.0.0
 * @access public
 * @param  string $layout_id The ID of the layout to get.
 * @return object|bool
 */
function carelib_get_layout( $layout_id ) {
	$layouts = carelib_get_layouts();

	return carelib_layout_exists( $layout_id ) ? $layouts[ $layout_id ] : false;
}

/**
 * Returns the default layout defined by the theme.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_default_layout() {
	$layouts = carelib_get_layouts();

	$layout_id = 'default';

	foreach ( (array) $layouts as $id => $object ) {
		if ( 'default' === $id ) {
			$name = $object->get_name( $layout_id );
		}
	}

	return $name;
}

/**
 * Returns the theme mod used for the global layout setting.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_global_layout() {
	return get_theme_mod( 'theme_layout', carelib_get_default_layout() );
}

/**
 * Get the theme layout.
 *
 * This is the global theme layout defined. Other functions filter the
 * available `theme_mod_theme_layout` hook to overwrite this.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_layout() {
	return apply_filters( "{$GLOBALS['carelib_prefix']}_get_theme_layout", carelib_get_global_layout() );
}

/**
 * Check whether a given post type has a specific layout.
 *
 * @since  1.0.0
 * @access public
 * @param  string         $post_type The post type to check.
 * @param  CareLib_Layout $layout The layout to check.
 * @return bool true if the current post type allows a given layout.
 */
function carelib_post_type_has_layout( $post_type, CareLib_Layout $layout ) {
	$post_types = $layout->get_post_types();

	if ( empty( $post_types ) ) {
		return true;
	}

	return in_array( $post_type, $post_types, true );
}

/**
 * Check a post if it has a specific layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $layout
 * @return bool
 */
function carelib_has_post_layout( CareLib_Layout $layout, $post_id = '' ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	return carelib_get_post_layout( $post_id ) === $layout ? true : false;
}

/**
 * Checks if a user/author has a specific layout.
 *
 * @since  1.0.0
 * @access public
 * @param  string $layout
 * @param  int $user_id
 * @return bool
 */
function carelib_has_user_layout( CareLib_Layout $layout, $user_id = '' ) {
	$user_id = empty( $user_id ) ? absint( get_query_var( 'author' ) ) : $user_id;

	return carelib_get_user_layout( $user_id ) === $layout ? true : false;
}

/**
 * Check whether the current layout includes a sidebar.
 *
 * @since  1.0.0
 * @access public
 * @param  string|array $sidebar_layouts A list of layouts which contain sidebars.
 * @return bool true if the current layout includes a sidebar
 */
function carelib_layout_has_sidebar( $sidebar_layouts ) {
	return ! in_array( carelib_get_theme_layout(), (array) $sidebar_layouts, true );
}

/**
 * Sets a post layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $post_id
 * @param  string $layout
 * @return bool
 */
function carelib_set_post_layout( $post_id, CareLib_Layout $layout ) {
	if ( 'default' !== $layout ) {
		return update_post_meta( $post_id, carelib_get_layout_meta_key(), $layout );
	}
	return carelib_delete_post_layout( $post_id );
}

/**
 * Sets a user layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $user_id
 * @param  string $layout
 * @return bool
 */
function carelib_set_user_layout( $user_id, CareLib_Layout $layout ) {
	if ( 'default' !== $layout ) {
		return update_user_meta( $user_id, carelib_get_layout_meta_key(), $layout );
	}
	return carelib_delete_user_layout( $user_id );
}

/**
 * Unregisters a layout object.
 *
 * @since  1.0.0
 * @access public
 * @param  string $name
 * @return void
 */
function carelib_unregister_layout( $name ) {
	$layouts = carelib_get_layouts();

	if ( carelib_layout_exists( $name ) ) {
		unset( $layouts[ $name ] );
	}
}

/**
 * Deletes a post layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $post_id
 * @return bool
 */
function carelib_delete_post_layout( $post_id ) {
	return delete_post_meta( $post_id, carelib_get_layout_meta_key() );
}

/**
 * Deletes user layout.
 *
 * @since  1.0.0
 * @access public
 * @param  int $user_id
 * @return bool
 */
function carelib_delete_user_layout( $user_id ) {
	return delete_user_meta( $user_id, carelib_get_layout_meta_key() );
}

/**
 * Determine whether or not a user should be able to control the layout.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function carelib_allow_layout_control() {
	return (bool) apply_filters( "{$GLOBALS['carelib_prefix']}_allow_layout_control", true );
}

/**
 * Determine whether or not a layout has been forced.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function carelib_is_layout_forced() {
	return ! carelib_allow_layout_control();
}

/**
 * Force a layout and return the id.
 *
 * @since  1.0.0
 * @access public
 * @param  string $layout_id the id of the layout to be forced.
 * @return string the slug of the forced layout.
 */
function carelib_force_layout( $layout_id ) {
	add_filter( "{$GLOBALS['carelib_prefix']}_allow_layout_control", '__return_false' );

	return $layout_id;
}
