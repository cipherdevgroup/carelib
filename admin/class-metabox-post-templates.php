<?php
/**
 * Adds the template meta box to the post editing screen for public post types.
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
class CareLib_Admin_Metabox_Post_Templates extends CareLib_Template_Hierarchy {

	protected static $templates = array();

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
	 * Adds the post template meta box for all public post types, excluding the
	 * 'page' post type since WordPress core already handles page templates.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $post_type
	 * @param  object  $post
	 * @return void
	 */
	public function add( $post_type, $post ) {
		$templates = $this->get_post_templates( $post_type );
		if ( ! empty( $templates ) && 'page' !== $post_type ) {
			add_meta_box(
				'carelib-post-template',
				esc_html__( 'Template', 'carelib' ),
				array( $this, 'box' ),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Displays the post template meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  object  $object
	 * @param  array   $box
	 * @return void
	 */
	public function box( $post, $box ) {
		$templates     = $this->get_post_templates( $post->post_type );
		$post_template = $this->get_post_template( $post->ID );

		require_once carelib()->get_dir() . 'admin/templates/metabox-post-template.php';
	}

	/**
	 * Saves the post template meta box settings as post metadata. Note that this meta is sanitized using the
	 * $this->sanitize_meta() callback function prior to being saved.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int      $post_id The ID of the current post being saved.
	 * @param  object   $post    The post object currently being saved.
	 * @return void|int
	 */
	public function save( $post_id, $post = '' ) {
		$no  = "{$this->prefix}_post_template_nonce";
		$act = "{$this->prefix}_update_post_template";

		// Verify the nonce for the post formats meta box.
		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
			return false;
		}

		$input   = isset( $_POST['carelib-post-template'] ) ? $_POST['carelib-post-template'] : '';
		$current = $this->get_post_template( $post_id );

		if ( $input === $current ) {
			return false;
		}

		if ( empty( $input ) ) {
			return $this->delete_post_template( $post_id );
		}

		return $this->set_post_template( $post_id, sanitize_text_field( $input ) );
	}

	/**
	 * Get an array of available custom templates with a specific header.
	 *
	 * Ideally, this function would be used to grab custom singular post templates.
	 * It is a recreation of the WordPress page templates function because it
	 * doesn't allow for other types of templates.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $post_type      The name of the post type to get templates for.
	 * @return array  $post_templates The array of templates.
	 */
	public function get_post_templates( $post_type = 'post' ) {
		if ( ! empty( self::$templates ) && isset( self::$templates[ $post_type ] ) ) {
			return self::$templates[ $post_type ];
		}

		$post_templates = array();

		// Get the theme PHP files one level deep.
		$files = wp_get_theme( get_template() )->get_files( 'php', 1 );

		// If a child theme is active, get its files and merge with the parent theme files.
		if ( is_child_theme() ) {
			$files = array_merge( $files, wp_get_theme()->get_files( 'php', 1 ) );
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

		return self::$templates[ $post_type ] = array_flip( $post_templates );
	}

}
