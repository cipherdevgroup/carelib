<?php
/**
 * The CareLib WordPress Theme Dashboard.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Dashboard {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Get things running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		if ( current_theme_supports( 'theme-dashboard' ) ) {
			$this->wp_hooks();
		}
	}

	/**
	 * Hook into WordPress.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'admin_init',            array( $this, 'register_settings' ),  10 );
		add_action( 'admin_menu',            array( $this, 'dashboard_menu' ),     10 );
		add_action( 'after_switch_theme',    array( $this, 'dashboard_setup' ),    10 );
		add_action( 'after_switch_theme',    array( $this, 'dashboard_redirect' ), 12 );
		add_action( 'switch_theme',          array( $this, 'dashboard_cleanup' ),  10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'dashboard_scripts' ),  10 );
		add_action( 'admin_notices',         array( $this, 'dashboard_notices' ),  10 );
	}

	/**
	 * Add options and fire a redirect when the theme is first activated.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  void
	 */
	function register_settings() {}

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
		add_option( "{$this->prefix}_dashboard_redirect", true );
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
		if ( is_network_admin() || ! get_option( "{$this->prefix}_dashboard_redirect" ) ) {
			return;
		}

		// Make sure this isn't run the next time the theme is activated.
		update_option( "{$this->prefix}_dashboard_redirect", false );

		wp_safe_redirect( admin_url( "index.php?page={$this->prefix}-dashboard" ) );
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
		delete_option( "{$this->prefix}_dashboard_redirect" );
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
		if ( is_object( $screen ) && "dashboard_page_{$this->prefix}-dashboard" === $screen->base ) {
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
		do_action( "{$this->prefix}_dashboard_notices" );
	}

	/**
	 * Add the WordPress Theme Dashboard to the main WordPress dashboard menu.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  void
	 */
	function dashboard_menu() {
		$theme = wp_get_theme();
		add_dashboard_page(
			$theme['Name'],
			$theme['Name'],
			'manage_options',
			"{$this->prefix}-dashboard",
			array( $this, 'dashboard_page' )
		);
	}

	/**
	 * Include the base template for our dashboard page.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  void
	 */
	public function dashboard_page() {
		require_once carelib()->get_dir() . 'admin/templates/dashboard.php';
	}

}
