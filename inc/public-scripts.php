<?php
/**
 * Methods for handling front-end JavaScript in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Public_Scripts extends CareLib_Scripts {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 5 );
	}

	/**
	 * Enqueue front-end scripts for the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

}
