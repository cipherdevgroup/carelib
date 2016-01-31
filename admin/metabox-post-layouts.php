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

/**
 * Register our metabox actions and filters.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_metabox_post_layouts_actions() {
	add_action( 'add_meta_boxes',  'carelib_metabox_post_layouts_add',  10, 2 );
	add_action( 'save_post',       'carelib_metabox_post_layouts_save', 10, 2 );
	add_action( 'add_attachment',  'carelib_metabox_post_layouts_save' );
	add_action( 'edit_attachment', 'carelib_metabox_post_layouts_save' );
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
function carelib_metabox_post_layouts_add( $post_type ) {
	$support = post_type_supports( $post_type, 'theme-layouts' );
	$control = carelib_allow_layout_control();

	if ( ! current_user_can( 'edit_theme_options' ) || ! $support || ! $control ) {
		return;
	}

	add_meta_box(
		'carelib-post-layout',
		esc_html__( 'Layout', 'carelib' ),
		'carelib_metabox_post_layouts_box',
		$post_type,
		'side',
		'default'
	);

	add_action( 'admin_enqueue_scripts', 'carelib_metabox_post_layouts_enqueue', 5 );
}

/**
 * Loads the scripts/styles for the layout meta box.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_metabox_post_layouts_enqueue() {
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
function carelib_metabox_post_layouts_box( $post, $box ) {
	$post_layout = 'default';
	if ( carelib_get_post_layout( $post->ID ) ) {
		$post_layout = carelib_get_post_layout( $post->ID );
	}

	$post_layouts = carelib_get_layouts();

	require_once carelib_get_dir() . 'admin/templates/metabox-post-layouts.php';
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
function carelib_metabox_post_layouts_save( $post_id, $post = '' ) {
	$no  = "{$GLOBALS['carelib_prefix']}_post_layout_nonce";
	$act = "{$GLOBALS['carelib_prefix']}_update_post_layout";

	// Verify the nonce for the post formats meta box.
	if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
		return false;
	}

	$input   = isset( $_POST['carelib-post-layout'] ) ? $_POST['carelib-post-layout'] : '';
	$current = carelib_get_post_layout( $post_id );

	if ( $input === $current ) {
		return false;
	}

	if ( empty( $input ) ) {
		return carelib_delete_post_layout( $post_id );
	}

	return carelib_set_post_layout( $post_id, sanitize_key( $input ) );
}
