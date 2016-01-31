<?php
/**
 * The CareLib WordPress Theme Dashboard.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get things running!
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_add_support() {
	$this->wp_hooks();
}

/**
 * Hook into WordPress.
 *
 * @since  0.2.0
 * @access protected
 * @return void
 */
function _carelib_wp_hooks() {
	add_action( 'admin_init',            'carelib_register_settings' ),  10 );
	add_action( 'admin_menu',            'carelib_dashboard_menu' ),     10 );
	add_action( 'after_switch_theme',    'carelib_dashboard_setup' ),    10 );
	add_action( 'after_switch_theme',    'carelib_dashboard_redirect' ), 12 );
	add_action( 'switch_theme',          'carelib_dashboard_cleanup' ),  10 );
	add_action( 'admin_enqueue_scripts', 'carelib_dashboard_scripts' ),  10 );
	add_action( 'admin_notices',         'carelib_dashboard_notices' ),  10 );
}

/**
 * Add options and fire a redirect when the theme is first activated.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function register_settings() {

}

/**
 * Set up the dashboard options.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_setup() {
	if ( is_network_admin() ) {
		return;
	}
	// Add the an option to redirect.
	add_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect", true );
}

/**
 * Add options and fire a redirect when the theme is first activated.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_redirect() {
	// Bail if this isn't the first time our theme has been activated.
	if ( is_network_admin() || ! get_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect" ) ) {
		return;
	}

	// Make sure this isn't run the next time the theme is activated.
	update_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect", false );

	wp_safe_redirect( admin_url( "index.php?page={$GLOBALS['carelib_prefix']}-dashboard" ) );
	exit;
}

/**
 * Remove any option that won't be reused by other Flagship products.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_cleanup() {
	delete_option( "{$GLOBALS['carelib_prefix']}_dashboard_redirect" );
}

/**
 * Helper function to find out if we're on the dashboard page.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function is_dashboard_page() {
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
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_scripts() {
	if ( $this->is_dashboard_page() ) {
		wp_enqueue_script( 'carelib-dashboard' );
		wp_enqueue_style( 'carelib-dashboard' );
	}
}

/**
 * Display all messages related to the Flagship dashboard.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_notices() {
	do_action( "{$GLOBALS['carelib_prefix']}_dashboard_notices" );
}

/**
 * Add the WordPress Theme Dashboard to the main WordPress dashboard menu.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function dashboard_menu() {
	$theme = carelib_get_theme();
	add_theme_page(
		$theme['Name'],
		$theme['Name'],
		'manage_options',
		"{$GLOBALS['carelib_prefix']}-dashboard",
		'carelib_dashboard_page' )
	);
}

/**
 * Include the base template for our dashboard page.
 *
 * @since   0.2.0
 * @access  public
 * @return  void
 */
function carelib_dashboard_page() {
	require_once carelib_get_dir( 'admin/templates/dashboard.php' );
}
