<?php
/**
 * Adds the template meta box to the post editing screen for public post types.
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
function carelib_metabox_post_template_actions() {
	add_action( 'add_meta_boxes',  'carelib_metabox_post_template_add',  10, 2 );
	add_action( 'save_post',       'carelib_metabox_post_template_save', 10, 2 );
	add_action( 'add_attachment',  'carelib_metabox_post_template_save' );
	add_action( 'edit_attachment', 'carelib_metabox_post_template_save' );
}

/**
 * Adds the post template meta box for all public post types, excluding the
 * 'page' post type since WordPress core already handles page templates.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function carelib_metabox_post_template_add( $post_type, $post ) {
	$templates = carelib_get_post_templates( $post_type );

	if ( ! empty( $templates ) && 'page' !== $post_type ) {
		add_meta_box(
			'carelib-post-template',
			esc_html__( 'Template', 'carelib' ),
			'carelib_metabox_post_template_box',
			$post_type,
			'side',
			'default'
		);
	}
}

/**
 * Displays the post template meta box.
 *
 * @since  1.0.0
 * @access public
 * @param  object $post
 * @param  array  $box
 * @return void
 */
function carelib_metabox_post_template_box( $post, $box ) {
	$templates     = carelib_get_post_templates( $post->post_type );
	$post_template = carelib_get_post_template( $post->ID );

	require_once carelib_get_dir( 'admin/templates/metabox-post-templates.php' );
}

/**
 * Saves the post template meta box settings as post metadata.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $post_id The ID of the current post being saved.
 * @param  object $post    The post object currently being saved.
 * @return void|int
 */
function carelib_metabox_post_template_save( $post_id, $post = '' ) {
	$no  = "{$GLOBALS['carelib_prefix']}_post_template_nonce";
	$act = "{$GLOBALS['carelib_prefix']}_update_post_template";

	// Verify the nonce for the post formats meta box.
	if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
		return false;
	}

	$input   = isset( $_POST['carelib-post-template'] ) ? $_POST['carelib-post-template'] : '';
	$current = carelib_get_post_template( $post_id );

	if ( $input === $current ) {
		return false;
	}

	if ( empty( $input ) ) {
		return carelib_delete_post_template( $post_id );
	}

	return carelib_set_post_template( $post_id, sanitize_text_field( $input ) );
}

/**
 * Get an array of available custom templates with a specific header.
 *
 * Ideally, this function would be used to grab custom singular post templates.
 * It is a recreation of the WordPress page templates function because it
 * doesn't allow for other types of templates.
 *
 * @since  1.0.0
 * @access public
 * @param  string $post_type      The name of the post type to get templates for.
 * @return array  $post_templates The array of templates.
 */
function carelib_get_post_templates( $post_type = 'post' ) {
	static $templates;

	if ( ! empty( $templates ) && isset( $templates[ $post_type ] ) ) {
		return $templates[ $post_type ];
	}

	$post_templates = array();

	// Get the theme PHP files one level deep.
	$files = carelib_get_parent()->get_files( 'php', 1 );

	// If a child theme is active, get its files and merge with the parent theme files.
	if ( is_child_theme() ) {
		$files = array_merge( $files, carelib_get_theme()->get_files( 'php', 1 ) );
	}

	foreach ( $files as $file => $path ) {
		// Get file data based on the post type singular name.
		$headers = get_file_data(
			$path,
			array( "{$post_type} Template" => "{$post_type} Template" )
		);

		if ( ! empty( $headers[ "{$post_type} Template" ] ) ) {
			$post_templates[ $file ] = $headers[ "{$post_type} Template" ];
		}
	}

	return $templates[ $post_type ] = array_flip( $post_templates );
}
