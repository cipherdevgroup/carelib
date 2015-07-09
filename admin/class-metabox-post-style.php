<?php
/**
 * Adds the post style meta box to the edit post screen.
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
class CareLib_Admin_Metabox_Post_Style extends CareLib_Admin_Scripts {

	protected static $post_styles = array();

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @uses   CareLib_Admin_Metabox_Post_Layout::$wp_hooks
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
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
	 * Adds the style meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $post_type
	 * @param  object  $post
	 * @return void
	 */
	public function add( $post_type, $post ) {
		$styles = $this->get_post_styles( $post_type );
		if ( ! empty( $styles ) && current_user_can( 'edit_theme_options' ) ) {
			add_meta_box(
				'carelib-post-style',
				esc_html__( 'Style', 'carelib' ),
				array( $this, 'box' ),
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
	public function box( $post, $box ) {
		$styles     = $this->get_post_styles( $post->post_type );
		$post_style = $this->get_post_style( $post->ID );

		require_once carelib()->get_dir() . 'admin/templates/metabox-post-style.php';
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
	public function save( $post_id, $post = '' ) {
		if ( ! is_object( $post ) ) {
			$post = get_post();
		}

		$no  = 'carelib_post_style_nonce';
		$act = 'carelib_update_post_style';

		// Verify the nonce for the post formats meta box.
		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
			return $post_id;
		}

		$data    = isset( $_POST['carelib-post-style'] ) ? $_POST['carelib-post-style'] : '';
		$current = $this->get_post_layout( $post_id );

		if ( '' === $data && $current ) {
			$this->delete_post_style( $post_id );
		} elseif ( $current !== $data ) {
			$this->set_post_style( $post_id, $data );
		}
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
	public function get_post_styles( $post_type = 'post' ) {
		if ( ! empty( $this->post_styles[ $post_type ] ) ) {
			return $this->post_styles[ $post_type ];
		}

		// Set up an empty styles array.
		$this->post_styles[ $post_type ] = array();

		// Get the theme CSS files two levels deep.
		$files = wp_get_theme( get_template() )->get_files( 'css', 2 );

		// If a child theme is active, get its files and merge with the parent theme files.
		if ( is_child_theme() ) {
			$files = array_merge( $files, wp_get_theme()->get_files( 'css', 2 ) );
		}

		// Loop through each of the CSS files and check if they are styles.
		foreach ( $files as $file => $path ) {
			// Get file data based on the 'Style Name' header.
			$headers = get_file_data(
				$path,
				array(
					'Style Name'         => 'Style Name',
					"{$post_type} Style" => "{$post_type} Style",
				)
			);

			// Add the CSS filename and template name to the array.
			if ( ! empty( $headers['Style Name'] ) ) {
				$this->post_styles[ $post_type ][ $file ] = $headers['Style Name'];
			} elseif ( ! empty( $headers[ "{$post_type} Style" ] ) ) {
				$this->post_styles[ $post_type ][ $file ] = $headers[ "{$post_type} Style" ];
			}
		}

		// Flip the array of styles.
		$this->post_styles[ $post_type ] = array_flip( $this->post_styles[ $post_type ] );

		// Return array of styles.
		return $this->post_styles[ $post_type ];
	}

}
