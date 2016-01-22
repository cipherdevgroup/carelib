<?php
/**
 * Methods for handling admin JavaScript and CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Styles extends CareLib_Styles {
	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @uses   CareLib_Admin_Metabox_Post_Layout::$wp_hooks
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register' ), 0 );
	}

	/**
	 * Registers admin styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register() {
		wp_register_style(
			'carelib-admin',
			carelib_get( 'paths' )->get_css_uri( "carelib-admin{$this->suffix}.css" ),
			null,
			$this->version
		);
		wp_register_style(
			'carelib-dashboard',
			carelib_get( 'paths' )->get_css_uri( "carelib-dashboard{$this->suffix}.css" ),
			null,
			$this->version
		);
	}
}
