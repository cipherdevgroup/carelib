<?php
/**
 * Adds the layout meta box to the post editing screen for post types that
 * support `theme-layouts`.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Metabox_Post_Layouts {
	/**
	 * Placeholder for the CareLib_Layouts_Hooks class.
	 *
	 * @since 0.2.0
	 * @var   object
	 */
	protected $layouts;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @uses   CareLib_Admin_Metabox_Post_Layout::$wp_hooks
	 * @return void
	 */
	public function add_layouts_support() {
		$this->layouts = carelib_get( 'layouts-hooks' )->load_metabox( $this );
	}

	/**
	 * Register our metabox actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function setup_metabox() {
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
		$support = post_type_supports( $post_type, 'theme-layouts' );
		$control = $this->layouts->allow_layout_control();
		if ( ! current_user_can( 'edit_theme_options' ) || ! $support || ! $control ) {
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
		$post_layout  = $this->layouts->get_post_layout( $post->ID );
		$post_layout  = $post_layout ? $post_layout : 'default';
		$post_layouts = $this->layouts->get_layouts();
		require_once carelib_get( 'paths' )->get_dir() . 'admin/templates/metabox-post-layouts.php';
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
		$current = $this->layouts->get_post_layout( $post_id );

		if ( $input === $current ) {
			return false;
		}

		if ( empty( $input ) ) {
			return $this->layouts->delete_post_layout( $post_id );
		}

		return $this->layouts->set_post_layout( $post_id, sanitize_key( $input ) );
	}
}
