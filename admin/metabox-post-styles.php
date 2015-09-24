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

class CareLib_Admin_Metabox_Post_Styles extends CareLib_Admin_Styles {

	protected static $styles = array();

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
	 * @access protected
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
		$no  = "{$this->prefix}_post_style_nonce";
		$act = "{$this->prefix}_update_post_style";

		// Verify the nonce for the post formats meta box.
		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], $act ) ) {
			return false;
		}

		$input   = isset( $_POST['carelib-post-style'] ) ? $_POST['carelib-post-style'] : '';
		$current = $this->get_post_style( $post_id );

		if ( $input === $current ) {
			return false;
		}

		if ( empty( $input ) ) {
			return $this->delete_post_style( $post_id );
		}

		return $this->set_post_style( $post_id, $input );
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
		if ( ! empty( self::$styles[ $post_type ] ) ) {
			return self::$styles[ $post_type ];
		}

		self::$styles[ $post_type ] = array();

		$files = wp_get_theme( get_template() )->get_files( 'css', 2 );

		if ( is_child_theme() ) {
			$files = array_merge( $files, wp_get_theme()->get_files( 'css', 2 ) );
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
				self::$styles[ $post_type ][ $file ] = $headers['Style Name'];
			} elseif ( ! empty( $headers[ "{$post_type} Style" ] ) ) {
				self::$styles[ $post_type ][ $file ] = $headers[ "{$post_type} Style" ];
			}
		}

		return self::$styles[ $post_type ] = array_flip( self::$styles[ $post_type ] );
	}

}
