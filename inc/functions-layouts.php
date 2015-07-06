<?php
/**
 * Layouts API - An API for themes to build layout options.
 *
 * Theme Layouts was created to allow theme developers to easily style themes with dynamic layout
 * structures. This file merely contains the API function calls at theme developers' disposal.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

# Registers default layouts.
add_action( 'init', 'carelib_register_layouts', 95 );

# Filters `current_theme_supports( 'theme-layouts', $arg )`.
add_filter( 'current_theme_supports-theme-layouts', 'carelib_theme_layouts_support', 10, 3 );

# Filters the theme layout mod.
add_filter( 'theme_mod_theme_layout', 'carelib_filter_layout', 5 );

/**
 * Returns the instance of the `CareLib_Layout_Factory` object. Use this function to access the object.
 *
 * @see    CareLib_Layout_Factory
 * @since  3.0.0
 * @access public
 * @return object
 */
function carelib_layouts() {
	return CareLib_Layout_Factory::get_instance();
}

/**
 * Registers the default theme layouts.
 *
 * @since  3.0.0
 * @access public
 * @return void
 */
function carelib_register_layouts() {

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

	// Hook for registering theme layouts. Theme should always register on this hook.
	do_action( 'carelib_register_layouts' );
}

/**
 * Function for registering a layout.
 *
 * @see    CareLib_Layout_Factory::register_layout()
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function carelib_register_layout( $name, $args = array() ) {
	carelib_layouts()->register_layout( $name, $args );
}

/**
 * Unregisters a layout.
 *
 * @see    CareLib_Layout_Factory::unregister_layout()
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function carelib_unregister_layout( $name ) {
	carelib_layouts()->unregister_layout( $name );
}

/**
 * Checks if a layout exists.
 *
 * @see    CareLib_Layout_Factory::layout_exists()
 * @since  3.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function carelib_layout_exists( $name ) {
	return carelib_layouts()->layout_exists( $name );
}

/**
 * Returns an array of registered layout objects.
 *
 * @see    CareLib_Layout_Factory::layout
 * @since  3.0.0
 * @access public
 * @return array
 */
function carelib_get_layouts() {
	return carelib_layouts()->layouts;
}

/**
 * Returns a layout object if it exists.  Otherwise, `FALSE`.
 *
 * @see    CareLib_Layout_Factory::get_layout()
 * @see    CareLib_Layout
 * @since  3.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function carelib_get_layout( $name ) {
	return carelib_layouts()->get_layout( $name );
}

/**
 * Gets the theme layout.  This is the global theme layout defined. Other functions filter the
 * available `theme_mod_theme_layout` hook to overwrite this.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_layout() {
	return get_theme_mod( 'theme_layout', carelib_get_default_layout() );
}

/**
 * Returns the default layout defined by the theme.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function carelib_get_default_layout() {
	$support = get_theme_support( 'theme-layouts' );

	return isset( $support[0] ) && isset( $support[0]['default'] ) ? $support[0]['default'] : 'default';
}

/**
 * Gets a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function carelib_get_post_layout( $post_id ) {
	return get_post_meta( $post_id, carelib_get_layout_meta_key(), true );
}

/**
 * Sets a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @param  string  $layout
 * @return bool
 */
function carelib_set_post_layout( $post_id, $layout ) {
	return 'default' !== $layout ? update_post_meta( $post_id, carelib_get_layout_meta_key(), $layout ) : carelib_delete_post_layout( $post_id );
}

/**
 * Deletes a post layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function carelib_delete_post_layout( $post_id ) {
	return delete_post_meta( $post_id, carelib_get_layout_meta_key() );
}

/**
 * Checks a post if it has a specific layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $post_id
 * @return bool
 */
function carelib_has_post_layout( $layout, $post_id = '' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return carelib_get_post_layout( $post_id ) === $layout ? true : false;
}

/**
 * Gets a user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function carelib_get_user_layout( $user_id ) {
	return get_user_meta( $user_id, carelib_get_layout_meta_key(), true );
}

/**
 * Sets a user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @param  string  $layout
 * @return bool
 */
function carelib_set_user_layout( $user_id, $layout ) {
	return 'default' !== $layout ? update_user_meta( $user_id, carelib_get_layout_meta_key(), $layout ) : carelib_delete_user_layout( $user_id );
}

/**
 * Deletes user layout.
 *
 * @since  3.0.0
 * @access public
 * @param  int     $user_id
 * @return bool
 */
function carelib_delete_user_layout( $user_id ) {
	return delete_user_meta( $user_id, carelib_get_layout_meta_key() );
}

/**
 * Checks if a user/author has a specific layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $layout
 * @param  int     $user_id
 * @return bool
 */
function carelib_has_user_layout( $layout, $user_id = '' ) {
	if ( ! $user_id ) {
		$user_id = absint( get_query_var( 'author' ) );
	}

	return carelib_get_user_layout( $user_id ) === $layout ? true : false;
}

/**
 * Default filter on the `theme_mod_theme_layout` hook.  By default, we'll check for per-post
 * or per-author layouts saved as metadata.  If set, we'll filter.  Else, just return the
 * global layout.
 *
 * @since  3.0.0
 * @access public
 * @param  string  $theme_layout
 * @return string
 */
function carelib_filter_layout( $theme_layout ) {
	// If viewing a singular post, get the post layout.
	if ( is_singular() ) {
		$layout = carelib_get_post_layout( get_queried_object_id() );
	} elseif ( is_author() ) {
		// If viewing an author archive, get the user layout.
		$layout = carelib_get_user_layout( get_queried_object_id() );
	}

	return ! empty( $layout ) && 'default' !== $layout ? $layout : $theme_layout;
}

/**
 * Returns an array of the available theme layouts.
 *
 * @since  3.0.0
 * @access public
 * @param  bool   $supports
 * @param  array  $args
 * @param  array  $feature
 * @return bool
 */
function carelib_theme_layouts_support( $supports, $args, $feature ) {
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
 * @since  3.0.0
 * @access public
 * @return string
 */
function carelib_get_layout_meta_key() {
	return apply_filters( 'carelib_layout_meta_key', 'Layout' );
}
