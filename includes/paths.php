<?php
/**
 * The main CareLib library class.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

/**
 * Return the path to the CareLib directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the library directory.
 * @return string
 */
function carelib_get_dir( $path = '' ) {
	return CARELIB_DIR . ltrim( $path );
}

/**
 * Return the URI to the CareLib directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the library URI.
 * @return string
 */
function carelib_get_uri( $path = '' ) {
	$relpath = (string) apply_filters( 'carelib_relative_uri_path', 'includes/vendor' );

	return sprintf( '%s/%s/%s/%s',
		untrailingslashit( get_template_directory_uri() ),
		untrailingslashit( trim( $relpath ) ),
		basename( CARELIB_DIR ),
		ltrim( $path )
	);
}

/**
 * Return the path to the library css directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the library CSS URI.
 * @return string
 */
function carelib_get_css_uri( $path ) {
	return carelib_get_uri( 'css/' ) . ltrim( $path );
}

/**
 * Return the path to the library JS directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the library JS URI.
 * @return string
 */
function carelib_get_js_uri( $path ) {
	return carelib_get_uri( 'js/' ) . ltrim( $path );
}

/**
 * Return the path to the parent theme directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the parent directory.
 * @return string
 */
function carelib_get_parent_dir( $path = '' ) {
	return PARENT_THEME_DIR . ltrim( $path );
}

/**
 * Return the path to the parent theme URI with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the parent URI.
 * @return string
 */
function carelib_get_parent_uri( $path = '' ) {
	return PARENT_THEME_URI . ltrim( $path );
}

/**
 * Return the path to the child theme directory with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the child directory.
 * @return string
 */
function carelib_get_child_dir( $path = '' ) {
	return CHILD_THEME_DIR . ltrim( $path );
}

/**
 * Return the path to the child theme URI with a trailing slash.
 *
 * @since  1.0.0
 * @access public
 * @param  string $path An optional path to append to the child URI.
 * @return string
 */
function carelib_get_child_uri( $path = '' ) {
	return CHILD_THEME_URI . ltrim( $path );
}
