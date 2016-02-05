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
 * Fix asset directory path on Windows installations.
 *
 * Because we don't know where the library is located, we need to
 * generate a URI based on the library directory path. In order to do
 * this, we are replacing the theme root directory portion of the
 * library directory with the theme root URI.
 *
 * @since  1.0.0
 * @access protected
 * @param  string $path the absolute path to the library's root directory.
 * @uses   trailingslashit()
 * @uses   get_template_directory()
 * @uses   get_theme_root_uri()
 * @return string a normalized uri string.
 */
function _carelib_normalize_uri( $path ) {
	return str_replace(
		wp_normalize_path( get_theme_root() ),
		get_theme_root_uri(),
		wp_normalize_path( $path )
	);
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
	return _carelib_normalize_uri( CARELIB_DIR ) . ltrim( $path );
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
