<?php
/**
 * Load all required library files.
 *
 * @package    CareLib
 * @subpackage CareLib\Init
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $GLOBALS['carelib_prefix'] ) ) {
	$GLOBALS['carelib_prefix'] = 'carelib';
}

/**
 * The current version of CareLib.
 *
 * @since 0.1.0
 */
define( 'CARELIB_VERSION', '0.1.0' );

/**
 * The absolute path to CareLib's root directory with a trailing slash.
 *
 * @since 0.1.0
 * @uses  get_template_directory()
 * @uses  trailingslashit()
 */
define( 'CARELIB_DIR', trailingslashit( dirname( __FILE__ ) ) );

/**
 * The absolute path to the template's root directory with a trailing slash.
 *
 * @since 0.1.0
 * @uses  get_template_directory_uri()
 * @uses  trailingslashit()
 */
define( 'CARELIB_URI', trailingslashit( get_theme_root_uri() ) . strstr( wp_normalize_path( dirname( __FILE__ ) ), basename( get_template_directory() ) ) );

if ( ! defined( 'PARENT_THEME_DIR' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 0.1.0
	 * @uses  get_template_directory()
	 * @uses  trailingslashit()
	 */
	define( 'PARENT_THEME_DIR', trailingslashit( get_template_directory() ) );
}

if ( ! defined( 'PARENT_THEME_URI' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 0.1.0
	 * @uses  get_template_directory_uri()
	 * @uses  trailingslashit()
	 */
	define( 'PARENT_THEME_URI', trailingslashit( get_template_directory_uri() ) );
}

if ( ! defined( 'CHILD_THEME_DIR' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 0.1.0
	 * @uses  get_stylesheet_directory()
	 * @uses  trailingslashit()
	 */
	define( 'CHILD_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'PARENT_THEME_URI' ) ) {
	/**
	 * The absolute path to the template's root directory with a trailing slash.
	 *
	 * @since 0.1.0
	 * @uses  get_stylesheet_directory_uri()
	 * @uses  trailingslashit()
	 */
	define( 'CHILD_THEME_URI', trailingslashit( get_stylesheet_directory_uri() ) );
}

add_action( 'after_setup_theme', 'carelib_includes', -95 );
function carelib_includes() {
	require_once CARELIB_DIR . 'inc/attributes.php';
	require_once CARELIB_DIR . 'inc/breadcrumbs.php';
	require_once CARELIB_DIR . 'inc/cache-cleanup.php';
	require_once CARELIB_DIR . 'inc/class-layout.php';
	require_once CARELIB_DIR . 'inc/context.php';
	require_once CARELIB_DIR . 'inc/head.php';
	require_once CARELIB_DIR . 'inc/image.php';
	require_once CARELIB_DIR . 'inc/language.php';
	require_once CARELIB_DIR . 'inc/layouts.php';
	require_once CARELIB_DIR . 'inc/menu.php';
	require_once CARELIB_DIR . 'inc/meta.php';
	require_once CARELIB_DIR . 'inc/paths.php';
	require_once CARELIB_DIR . 'inc/scripts.php';
	require_once CARELIB_DIR . 'inc/search-form.php';
	require_once CARELIB_DIR . 'inc/sidebar.php';
	require_once CARELIB_DIR . 'inc/styles-post.php';
	require_once CARELIB_DIR . 'inc/styles.php';
	require_once CARELIB_DIR . 'inc/support.php';
	require_once CARELIB_DIR . 'inc/template-archive.php';
	require_once CARELIB_DIR . 'inc/template-comments.php';
	require_once CARELIB_DIR . 'inc/template-entry.php';
	require_once CARELIB_DIR . 'inc/template-global.php';
	require_once CARELIB_DIR . 'inc/template-hierarchy.php';
	require_once CARELIB_DIR . 'inc/template-hooks.php';
	require_once CARELIB_DIR . 'inc/theme.php';
	require_once CARELIB_DIR . 'inc/tinymce.php';
	require_once CARELIB_DIR . 'inc/actions.php';
	require_once CARELIB_DIR . 'inc/filters.php';
}

add_action( 'after_setup_theme', 'carelib_admin_includes', -95 );
function carelib_admin_includes() {
	if ( is_admin() ) {
	//	require_once CARELIB_DIR . 'admin/dashboard.php';
		require_once CARELIB_DIR . 'admin/metabox-post-layouts.php';
		require_once CARELIB_DIR . 'admin/metabox-post-styles.php';
		require_once CARELIB_DIR . 'admin/metabox-post-templates.php';
		require_once CARELIB_DIR . 'admin/scripts.php';
		require_once CARELIB_DIR . 'admin/styles.php';
		require_once CARELIB_DIR . 'admin/actions.php';
	}
}

add_action( 'after_setup_theme', 'carelib_customize_includes', -95 );
function carelib_customize_includes() {
	if ( is_customize_preview() ) {
		require_once CARELIB_DIR . 'customize/control-radio-image.php';
		require_once CARELIB_DIR . 'customize/control-layout.php';
		require_once CARELIB_DIR . 'customize/register-breadcrumbs.php';
		require_once CARELIB_DIR . 'customize/register-layouts.php';
		require_once CARELIB_DIR . 'customize/scripts.php';
		require_once CARELIB_DIR . 'customize/styles.php';
		require_once CARELIB_DIR . 'customize/actions.php';
	}
}
