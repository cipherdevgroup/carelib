<?php
/**
 * Adds the layout meta box to the post editing screen for post types that
 * support `theme-layouts`.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * A class to load a layout metabox on the post edit screen.
 *
 * @package CareLib
 */
class CareLib_Admin_Metabox_Post_Layouts extends CareLib_Layouts {

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
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'load-post.php',     array( $this, 'metabox_hooks' ) );
		add_action( 'load-post-new.php', array( $this, 'metabox_hooks' ) );
	}

	/**
	 * Register our metabox actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function metabox_hooks() {
		add_action( 'add_meta_boxes',  array( $this, 'add' ),  10, 2 );
		add_action( 'save_post',       array( $this, 'save' ), 10, 2 );
		add_action( 'add_attachment',  array( $this, 'save' ) );
		add_action( 'edit_attachment', array( $this, 'save' ) );
	}

	/**
	 * Adds the layout meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $post_type
	 * @param  object  $post
	 * @return void
	 */
	public function add( $post_type ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		add_meta_box(
			'carelib-post-layout',
			esc_html__( 'Layout', 'carelib' ),
			array( $this, 'box' ),
			$post_type,
			'side',
			'default'
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 5 );
	}

	/**
	 * Loads the scripts/styles for the layout meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'carelib-admin' );
	}

	/**
	 * Callback function for displaying the layout meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  object  $object
	 * @param  array   $box
	 * @return void
	 */
	public function box( $post, $box ) {
		$post_layout  = $this->get_post_layout( $post->ID );
		$post_layout  = $post_layout ? $post_layout : 'default';
		$post_layouts = $this->get_layouts();
		require_once carelib()->get_dir() . 'admin/templates/metabox-post-layouts.php';
	}

	/**
	 * Saves the post layout when submitted via the layout meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int      $post_id The ID of the current post being saved.
	 * @param  object   $post    The post object currently being saved.
	 * @return void|int
	 */
	public function save( $post_id, $post = '' ) {
		$no  = "{$this->prefix}_post_layout_nonce";
		$act = "{$this->prefix}_update_post_layout";

		// Verify the nonce for the post formats meta box.
		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
			return false;
		}

		$input   = isset( $_POST['carelib-post-layout'] ) ? $_POST['carelib-post-layout'] : '';
		$current = $this->get_post_layout( $post_id );

		if ( $input === $current ) {
			return false;
		}

		if ( empty( $input ) ) {
			return $this->delete_post_layout( $post_id );
		}

		return $this->set_post_layout( $post_id, sanitize_key( $input ) );
	}

}
