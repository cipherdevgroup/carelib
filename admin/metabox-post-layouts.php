<?php
/**
 * Adds the layout meta box to the post editing screen if layouts exist.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Register our metabox actions and filters.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_metabox_post_layouts_actions() {
	if ( carelib_has_layouts() ) {
		add_action( 'add_meta_boxes',  'carelib_metabox_post_layouts_add' );
		add_action( 'save_post',       'carelib_metabox_post_layouts_save', 10, 2 );
		add_action( 'add_attachment',  'carelib_metabox_post_layouts_save' );
		add_action( 'edit_attachment', 'carelib_metabox_post_layouts_save' );
	}
}

/**
 * Adds the layout meta box.
 *
 * @since  1.0.0
 * @access public
 * @param  string $post_type The post type for the current view.
 * @return void
 */
function carelib_metabox_post_layouts_add( $post_type ) {
	if ( ! current_user_can( 'edit_theme_options' ) || ! carelib_allow_layout_control() ) {
		return;
	}

	$obj = get_post_type_object( $post_type );

	if ( ! is_object( $obj ) || ! $obj->public ) {
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

	wp_enqueue_style( 'carelib-admin' );
}

/**
 * Callback function for displaying the layout meta box.
 *
 * @since  1.0.0
 * @access public
 * @param  object $post The current WordPress post object.
 * @param  array  $box The current Meta box data.
 * @return void
 */
function carelib_metabox_post_layouts_box( $post, $box ) {
	$current_layout = 'default';
	if ( carelib_get_post_layout( $post->ID ) ) {
		$current_layout = carelib_get_post_layout( $post->ID );
	}

	require_once carelib_get_dir( 'admin/templates/metabox-post-layouts.php' );
}

/**
 * Saves the post layout when submitted via the layout meta box.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $post_id The ID of the current post being saved.
 * @param  object $post    The post object currently being saved.
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
