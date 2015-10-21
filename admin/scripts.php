<?php
/**
 * Methods for handling admin JavaScript and CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Scripts extends CareLib_Scripts {
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
		add_action( 'admin_enqueue_scripts',  array( $this, 'register' ), 0 );
	}

	/**
	 * Registers admin styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register() {
		wp_register_script(
			'carelib-dashboard',
			$this->js_uri( "carelib-dashboard{$this->suffix}.js" ),
			array( 'jquery-ui-tabs' ),
			$this->version,
			true
		);
	}
}
