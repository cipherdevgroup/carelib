<?php
/**
 * Adds the post style meta box to the edit post screen.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Register our metabox actions and filters.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_metabox_post_styles_actions() {
	add_action( 'add_meta_boxes',  'carelib_metabox_post_styles_add',  10, 2 );
	add_action( 'save_post',       'carelib_metabox_post_styles_save', 10, 2 );
	add_action( 'add_attachment',  'carelib_metabox_post_styles_save' );
	add_action( 'edit_attachment', 'carelib_metabox_post_styles_save' );
}

/**
 * Adds the style meta box.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function carelib_metabox_post_styles_add( $post_type, $post ) {
	$styles = carelib_get_post_styles( $post_type );
	if ( ! empty( $styles ) && current_user_can( 'edit_theme_options' ) ) {
		add_meta_box(
			'carelib-post-style',
			esc_html__( 'Style', 'carelib' ),
			'carelib_metabox_post_styles_box',
			$post_type,
			'side',
			'default'
		);
	}
}

/**
 * Callback function for displaying the style meta box.
 *
 * @since  0.2.0
 * @access public
 * @param  object  $object
 * @param  array   $box
 * @return void
 */
function carelib_metabox_post_styles_box( $post, $box ) {
	$styles     = carelib_get_post_styles( $post->post_type );
	$post_style = carelib_get_post_style( $post->ID );

	require_once carelib_get_dir( 'admin/templates/metabox-post-style.php' );
}

/**
 * Saves the post style when submitted via the style meta box.
 *
 * @since  0.2.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function carelib_metabox_post_styles_save( $post_id, $post = '' ) {
	$no  = "{$GLOBALS['carelib_prefix']}_post_style_nonce";
	$act = "{$GLOBALS['carelib_prefix']}_update_post_style";

	// Verify the nonce for the post formats meta box.
	if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
		return false;
	}

	$input   = isset( $_POST['carelib-post-style'] ) ? $_POST['carelib-post-style'] : '';
	$current = carelib_get_post_style( $post_id );

	if ( $input === $current ) {
		return false;
	}

	if ( empty( $input ) ) {
		return carelib_delete_post_style( $post_id );
	}

	return carelib_set_post_style( $post_id, $input );
}

/**
 * Gets the stylesheet files within the parent or child theme and checks if
 * they have the 'Style Name' header. If any files are found, they are
 * returned in an array.
 *
 * @since  0.2.0
 * @access public
 * @return array
 */
function carelib_get_post_styles( $post_type = 'post' ) {
	static $styles = array();

	if ( ! empty( $styles[ $post_type ] ) ) {
		return $styles[ $post_type ];
	}

	$styles[ $post_type ] = array();

	$files = carelib_get_parent()->get_files( 'css', 2 );

	if ( is_child_theme() ) {
		$files = array_merge( $files, carelib_get_theme()->get_files( 'css', 2 ) );
	}

	foreach ( $files as $file => $path ) {
		$headers = get_file_data(
			$path,
			array(
				'Style Name'         => 'Style Name',
				"{$post_type} Style" => "{$post_type} Style",
			)
		);

		if ( ! empty( $headers['Style Name'] ) ) {
			$styles[ $post_type ][ $file ] = $headers['Style Name'];
		} elseif ( ! empty( $headers[ "{$post_type} Style" ] ) ) {
			$styles[ $post_type ][ $file ] = $headers[ "{$post_type} Style" ];
		}
	}

	return $styles[ $post_type ] = array_flip( $styles[ $post_type ] );
}
