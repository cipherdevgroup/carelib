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
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function fonts_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_fonts_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts_scripts' ) );
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

	/**
	 * Register assets required by the fonts feature for enqueueing on-demand.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_fonts_scripts() {
		wp_register_script(
			'webfontloader',
			'https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js',
			array(),
			'1.5.18'
		);
	}

	/**
	 * Enqueue fonts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_fonts_scripts() {
		if ( ! $this->is_typekit_active() || is_customize_preview() ) {
			return;
		}

		// Enqueue the Typekit kit.
		$kit_id = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		wp_enqueue_script(
			'carelib-fonts-typekit',
			sprintf( 'https://use.typekit.net/%s.js', sanitize_key( $kit_id ) )
		);

		add_action( 'wp_head', array( $this, 'load_typekit_fonts' ) );
	}

	/**
	 * Load Typekit fonts when the kit script is enqueued.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function load_typekit_fonts() {
		if ( wp_script_is( 'carelib-fonts-typekit', 'done' ) ) {
			echo '<script>try{Typekit.load({ async: true });}catch(e){}</script>';
		}
	}

}
