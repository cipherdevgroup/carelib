<?php
/**
 * Metadata functions used in the core library.
 *
 * This file registers meta keys for use in WordPress in a safe manner by
 * setting up a custom sanitize callback.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Meta {
	/**
	 * The layouts class object.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Layouts
	 */
	protected $layouts;

	/**
	 * The styles class object.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Styles
	 */
	protected $styles;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->layouts = carelib_get( 'layouts' );
		$this->styles = carelib_get( 'public-styles' );
	}

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
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'init', array( $this, 'register_meta' ), 15 );
	}

	/**
	 * Registers the library's custom metadata keys and sets up the sanitize
	 * callback function.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_meta() {
		// Post templates meta.
		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			if ( 'page' !== $post_type ) {
				register_meta(
					'post',
					"_wp_{$post_type}_template",
					'sanitize_text_field',
					'__return_false'
				);
			}
		}

		// Theme layouts meta.
		register_meta(
			'post',
			$this->layouts->get_meta_key(),
			'sanitize_key',
			'__return_false'
		);
		register_meta(
			'user',
			$this->layouts->get_meta_key(),
			'sanitize_key',
			'__return_false'
		);

		// Post styles meta.
		register_meta(
			'post',
			$this->styles->get_style_meta_key(),
			'sanitize_text_field',
			'__return_false'
		);
	}
}
