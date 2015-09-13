<?php
/**
 * The library has its own template hierarchy that can be used instead of the
 * default WordPress template hierarchy.
 *
 * It was built to extend the default by making it smarter and more flexible.
 * The goal is to give theme developers and end users an easy-to-override system
 * that doesn't involve massive amounts of conditional tags within files.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Template_Hierarchy {

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
		add_filter( 'template_include',    array( $this, 'index_include' ),      95 );
		add_filter( 'tag_template',        array( $this, 'taxonomy_template' ),   5 );
		add_filter( 'category_template',   array( $this, 'taxonomy_template' ),   5 );
		add_filter( 'taxonomy_template',   array( $this, 'taxonomy_template' ),   5 );
		add_filter( 'single_template',     array( $this, 'singular_template' ),   5 );
		add_filter( 'page_template',       array( $this, 'singular_template' ),   5 );
		add_filter( 'front_page_template', array( $this, 'front_page_template' ), 5 ); // Doesn't work b/c bug with get_query_template().
		add_filter( 'frontpage_template',  array( $this, 'front_page_template' ), 5 );
		add_filter( 'comments_template',   array( $this, 'comments_template' ),   5 );
	}

	/**
	 * Override WP's default index.php template.
	 *
	 * Because we don't really use index.php, this prevents searching for
	 * templates multiple times when trying to load the default template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $template
	 * @return string $template
	 */
	public function index_include( $template ) {
		if ( get_index_template() === $template ) {
			return carelib_get( 'template-global' )->framework( apply_filters( "{$this->prefix}_index_template", null ) );
		}
		return $template;
	}

	/**
	 * Override WP's default template for category and tag archives.
	 *
	 * This allows better organization of taxonomy template files by making
	 * categories and post tags work the same way as other taxonomies.
	 *
	 * Hierarchy:
	 * -------------------------
	 * taxonomy-$taxonomy-$term.php
	 * taxonomy-$taxonomy.php
	 * taxonomy.php
	 * archive.php
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $template
	 * @return string Full path to file.
	 */
	public function taxonomy_template( $template ) {
		$term = get_queried_object();

		// Return the available templates.
		return locate_template( array(
			"taxonomy-{$term->taxonomy}-{$term->slug}.php",
			"taxonomy-{$term->taxonomy}.php",
			'taxonomy.php',
			'archive.php',
		) );
	}

	/**
	 * Override the default single (singular post) template.
	 *
	 * Post templates can be loaded using a custom post template, by slug, or
	 * by ID.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $template The default WordPress post template.
	 * @return string $template The theme post template after all templates have been checked for.
	 */
	public function singular_template( $template ) {
		$templates = array();

		// Get the queried post.
		$post = get_queried_object();

		// Check for a custom post template by custom field key '_wp_post_template'.
		$custom = $this->get_post_template( get_queried_object_id() );
		if ( $custom ) {
			$templates[] = $custom;
		}

		$templates[] = "{$post->post_type}-{$post->post_name}.php";
		$templates[] = "{$post->post_type}-{$post->ID}.php";
		$templates[] = "{$post->post_type}.php";
		$templates[] = "single-{$post->post_type}.php";
		$templates[] = 'single.php';
		$templates[] = 'singular.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Fix for the front page template handling in WordPress core.
	 *
	 * This overwrites "front-page.php" template if posts are to be shown on
	 * the front page. This way, the "front-page.php" template will only ever
	 * be used if an actual page is supposed to be shown on the front.
	 *
	 * @link   http://www.chipbennett.net/2013/09/14/home-page-and-front-page-and-templates-oh-my/
	 * @since  0.2.0
	 * @access public
	 * @param  string  $template
	 * @return string
	 */
	public function front_page_template( $template ) {
		return is_home() ? '' : $template;
	}

	/**
	 * Override the default comments template.
	 *
	 * This filter allows for a "comments-{$post_type}.php" template based on
	 * the post type of the current single post view. If this template is not
	 * found, it falls back to the default "comments.php" template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $template The comments template file name.
	 * @return string $template The theme comments template after all templates have been checked for.
	 */
	public function comments_template( $template ) {
		$templates = array();
		$post_type = get_post_type();

		// Allow for custom templates entered into comments_template( $file ).
		$template = str_replace( trailingslashit( get_template_directory() ), '', $template );

		if ( 'comments.php' !== $template ) {
			$templates[] = $template;
		}

		$templates[] = "template-parts/comments-{$post_type}.php";
		$templates[] = 'template-parts/comments.php';
		$templates[] = 'comments.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Get a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @return bool
	 */
	public function get_post_template( $post_id ) {
		return get_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ), true );
	}

	/**
	 * Set a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @param  string $template
	 * @return bool
	 */
	public function set_post_template( $post_id, $template ) {
		return update_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ), $template );
	}

	/**
	 * Delete a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @return bool
	 */
	public function delete_post_template( $post_id ) {
		return delete_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ) );
	}

	/**
	 * Check if a post of any post type has a custom template.
	 *
	 * This is the equivalent of WordPress' `is_page_template()` function with
	 * the exception that it works for all post types.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $template The name of the template to check for.
	 * @param  int $post_id
	 * @return bool
	 */
	public function has_post_template( $template = '', $post_id = '' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Get the post template, which is saved as metadata.
		$post_template = $this->get_post_template( $post_id );

		// If a specific template was input, check that the post template matches.
		if ( $template && $template === $post_template ) {
			return true;
		}

		// Return whether we have a post template.
		return ! empty( $post_template );
	}

	/**
	 * Return the post template meta key.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id
	 * @return string
	 */
	public function get_post_template_meta_key( $post_id ) {
		return sprintf( '_wp_%s_template', get_post_type( $post_id ) );
	}

}
