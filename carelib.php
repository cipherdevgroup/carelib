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
 * The prefix used by filters throughout the library.
 *
 * @since 1.0.0
 */
if ( ! isset( $GLOBALS['carelib_prefix'] ) ) {
	$GLOBALS['carelib_prefix'] = 'carelib';
}

$GLOBALS['carelib_prefix'] = sanitize_key( $GLOBALS['carelib_prefix'] );

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

add_action( 'after_setup_theme', 'carelib_includes', -95 );
/**
 * Include all library files within a hookable function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_includes() {
	require_once CARELIB_DIR . 'includes/attributes.php';
	require_once CARELIB_DIR . 'includes/breadcrumbs.php';
	require_once CARELIB_DIR . 'includes/class-layout.php';
	require_once CARELIB_DIR . 'includes/context.php';
	require_once CARELIB_DIR . 'includes/head.php';
	require_once CARELIB_DIR . 'includes/image.php';
	require_once CARELIB_DIR . 'includes/language.php';
	require_once CARELIB_DIR . 'includes/layouts.php';
	require_once CARELIB_DIR . 'includes/menu.php';
	require_once CARELIB_DIR . 'includes/meta.php';
	require_once CARELIB_DIR . 'includes/paths.php';
	require_once CARELIB_DIR . 'includes/scripts.php';
	require_once CARELIB_DIR . 'includes/search-form.php';
	require_once CARELIB_DIR . 'includes/sidebar.php';
	require_once CARELIB_DIR . 'includes/styles-post.php';
	require_once CARELIB_DIR . 'includes/styles.php';
	require_once CARELIB_DIR . 'includes/support.php';
	require_once CARELIB_DIR . 'includes/template-archive.php';
	require_once CARELIB_DIR . 'includes/template-comments.php';
	require_once CARELIB_DIR . 'includes/template-entry.php';
	require_once CARELIB_DIR . 'includes/template-global.php';
	require_once CARELIB_DIR . 'includes/template-hierarchy.php';
	require_once CARELIB_DIR . 'includes/template-hooks.php';
	require_once CARELIB_DIR . 'includes/theme.php';
	require_once CARELIB_DIR . 'includes/tinymce.php';
	require_once CARELIB_DIR . 'includes/actions.php';
	require_once CARELIB_DIR . 'includes/filters.php';
}

add_action( 'after_setup_theme', 'carelib_admin_includes', -95 );
/**
 * Include all admin files within a hookable function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_admin_includes() {
	if ( is_admin() ) {
		require_once CARELIB_DIR . 'admin/dashboard.php';
		require_once CARELIB_DIR . 'admin/metabox-post-layouts.php';
		require_once CARELIB_DIR . 'admin/metabox-post-styles.php';
		require_once CARELIB_DIR . 'admin/metabox-post-templates.php';
		require_once CARELIB_DIR . 'admin/scripts.php';
		require_once CARELIB_DIR . 'admin/styles.php';
		require_once CARELIB_DIR . 'admin/actions.php';
	}
}

add_action( 'after_setup_theme', 'carelib_customize_includes', -95 );
/**
 * Include all customizer files within a hookable function.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
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
