<?php
/**
 * Load all required library files.
 *
 * @package    CareLib
 * @subpackage CareLib\Init
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * The current version of CareLib.
 *
 * @since 1.0.0
 */
define( 'CARELIB_VERSION', '1.0.0' );

/**
 * The absolute path to CareLib's root directory with a trailing slash.
 *
 * @since 1.0.0
 * @uses  get_template_directory()
 * @uses  trailingslashit()
 */
define( 'CARELIB_DIR', trailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'PARENT_THEME_DIR' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 1.0.0
	 * @uses  get_template_directory()
	 * @uses  trailingslashit()
	 */
	define( 'PARENT_THEME_DIR', trailingslashit( get_template_directory() ) );
}

if ( ! defined( 'PARENT_THEME_URI' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 1.0.0
	 * @uses  get_template_directory_uri()
	 * @uses  trailingslashit()
	 */
	define( 'PARENT_THEME_URI', trailingslashit( get_template_directory_uri() ) );
}

if ( ! defined( 'CHILD_THEME_DIR' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 1.0.0
	 * @uses  get_stylesheet_directory()
	 * @uses  trailingslashit()
	 */
	define( 'CHILD_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'PARENT_THEME_URI' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 1.0.0
	 * @uses  get_stylesheet_directory_uri()
	 * @uses  trailingslashit()
	 */
	define( 'CHILD_THEME_URI', trailingslashit( get_stylesheet_directory_uri() ) );
}

/**
 * The prefix used by filters throughout the library.
 *
 * @since 1.0.0
 */
if ( ! isset( $GLOBALS['carelib_prefix'] ) ) {
	$GLOBALS['carelib_prefix'] = 'carelib';
}

/**
 * The global used to store all layout objects.
 *
 * @since 1.0.0
 */
if ( ! isset( $GLOBALS['_carelib_layouts'] ) ) {
	$GLOBALS['_carelib_layouts'] = array();
}

$GLOBALS['carelib_prefix'] = sanitize_key( $GLOBALS['carelib_prefix'] );

require_once CARELIB_DIR . 'includes/init.php';
