<?php
/**
 * The CareLib WordPress Theme Dashboard.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Add all of our theme dashboard actions if the current theme supports them.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_maybe_add_theme_dashboard() {
	if ( current_theme_supports( 'theme-dashboard' ) ) {
		add_action( 'admin_menu',            'carelib_dashboard_menu',      0 );
		add_action( 'after_switch_theme',    'carelib_dashboard_setup',    10 );
		add_action( 'after_switch_theme',    'carelib_dashboard_redirect', 12 );
		add_action( 'switch_theme',          'carelib_dashboard_cleanup',  10 );
		add_action( 'admin_enqueue_scripts', 'carelib_dashboard_scripts',  10 );
		add_action( 'admin_notices',         'carelib_dashboard_notices',  10 );
	}
}

/**
 * Set up the dashboard options.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_setup() {
	if ( is_network_admin() ) {
		return;
	}
	// Add the an option to redirect.
	add_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect", true );
}

/**
 * Add the WordPress Theme Dashboard to the main WordPress dashboard menu.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_menu() {
	$theme = carelib_get_theme();
	add_theme_page(
		$theme['Name'],
		$theme['Name'],
		'edit_theme_options',
		"{$GLOBALS['carelib_prefix']}-dashboard",
		'carelib_dashboard_page'
	);
}

/**
 * Add options and fire a redirect when the theme is first activated.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_redirect() {
	// Bail if this isn't the first time our theme has been activated.
	if ( is_network_admin() || ! get_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect" ) ) {
		return;
	}

	// Make sure this isn't run the next time the theme is activated.
	update_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect", false );

	wp_safe_redirect( admin_url( "themes.php?page={$GLOBALS['carelib_prefix']}-dashboard" ) );
	exit;
}

/**
 * Remove any option that won't be reused by other Flagship products.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_cleanup() {
	delete_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect" );
}

/**
 * Helper function to find out if we're on the dashboard page.
 *
 * @since   1.0.0
 * @access  public
 * @return  bool
 */
function carelib_is_dashboard_page() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}
	$screen = get_current_screen();
	if ( is_object( $screen ) && "dashboard_page_{$GLOBALS['carelib_prefix']}-dashboard" === $screen->base ) {
		return true;
	}
	return false;
}

/**
 * Load scripts and styles for the Flagship dashboard.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_scripts() {
	if ( carelib_is_dashboard_page() ) {
		wp_enqueue_script( 'carelib-dashboard' );
		wp_enqueue_style( 'carelib-dashboard' );
	}
}

/**
 * Display all messages related to the Flagship dashboard.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_notices() {
	do_action( "{$GLOBALS['carelib_prefix']}_dashboard_notices" );
}

/**
 * Include the base template for our dashboard page.
 *
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_page() {
	require_once carelib_get_dir( 'admin/templates/dashboard.php' );
}
