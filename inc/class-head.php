<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Head {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'wp_head', array( $this, 'meta_charset' ),  0 );
		add_action( 'wp_head', array( $this, 'meta_viewport' ), 1 );
		add_action( 'wp_head', array( $this, 'link_pingback' ), 3 );
	}

	/**
	 * Adds the meta charset to the header.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function meta_charset() {
		printf( '<meta charset="%s" />' . "\n", esc_attr( get_bloginfo( 'charset' ) ) );
	}

	/**
	 * Adds the meta viewport to the header.
	 *
	 * @since  0.2.0
	 * @access public
	 */
	public function meta_viewport() {
		echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
	}

	/**
	 * Adds the pingback link to the header.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function link_pingback() {
		if ( 'open' === get_option( 'default_ping_status' ) ) {
			printf( '<link rel="pingback" href="%s" />' . "\n",
				esc_url( get_bloginfo( 'pingback_url' ) )
			);
		}
	}
}
