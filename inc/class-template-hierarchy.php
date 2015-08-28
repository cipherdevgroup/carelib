<?php
/**
 * The framework has its own template hierarchy that can be used instead of the
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

/**
 * CareLib Filters class.
 */
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
		add_filter( 'date_template',       array( $this, 'date_template' ),       5 );
		add_filter( 'author_template',     array( $this, 'user_template' ),       5 );
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
	 * Overrides WP's default template for date-based archives. Better abstraction of templates than
	 * is_date() allows by checking for the year, month, week, day, hour, and minute.
	 *
	 * @since  0.6.0
	 * @access public
	 * @param  string $template
	 * @return string $template
	 */
	public function date_template( $template ) {
		$templates = array();

		// If viewing a time-based archive.
		if ( is_time() ) {
			// If viewing a minutely archive.
			if ( get_query_var( 'minute' ) ) {
				$templates[] = 'minute.php';
			}

			// If viewing an hourly archive.
			if ( get_query_var( 'hour' ) ) {
				$templates[] = 'hour.php';
			}

			// Catchall for any time-based archive.
			$templates[] = 'time.php';
		}

		// If viewing a daily archive.
		if ( is_day() ) {
			$templates[] = 'day.php';
		}

		// If viewing a weekly archive.
		if ( get_query_var( 'w' ) ) {
			$templates[] = 'week.php';
		}

		// If viewing a monthly archive.
		if ( is_month() ) {
			$templates[] = 'month.php';
		}

		// If viewing a yearly archive.
		if ( is_year() ) {
			$templates[] = 'year.php';
		}

		// Catchall template for date-based archives.
		$templates[] = 'date.php';

		// Fall back to the basic archive template.
		$templates[] = 'archive.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Overrides WP's default template for author-based archives. Better abstraction of templates than
	 * is_author() allows by allowing themes to specify templates for a specific author. The hierarchy is
	 * user-$nicename.php, $user-role-$role.php, user.php, author.php, archive.php.
	 *
	 * @since  0.7.0
	 * @access public
	 * @param  string $template
	 * @return string
	 */
	public function user_template( $template ) {
		$templates = array();

		// Get the user nicename.
		$name = get_the_author_meta( 'user_nicename', get_query_var( 'author' ) );

		// Get the user object.
		$user = new WP_User( absint( get_query_var( 'author' ) ) );

		// Add the user nicename template.
		$templates[] = "user-{$name}.php";

		// Add role-based templates for the user.
		if ( is_array( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				$templates[] = "user-role-{$role}.php";
			}
		}

		// Add a basic user template.
		$templates[] = 'user.php';

		// Add backwards compatibility with the WordPress author template.
		$templates[] = 'author.php';

		// Fall back to the basic archive template.
		$templates[] = 'archive.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Overrides WP's default template for category- and tag-based archives. This allows better
	 * organization of taxonomy template files by making categories and post tags work the same way as
	 * other taxonomies. The hierarchy is taxonomy-$taxonomy-$term.php, taxonomy-$taxonomy.php,
	 * taxonomy.php, archive.php.
	 *
	 * @since  0.7.0
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
	 * Overrides the default single (singular post) template. Post templates can be loaded using a custom
	 * post template, by slug, or by ID.
	 *
	 * Attachment templates are handled slightly differently. Rather than look for the slug or ID, templates
	 * can be loaded by attachment-$mime[0]_$mime[1].php, attachment-$mime[1].php, or attachment-$mime[0].php.
	 *
	 * @since  0.7.0
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

		// Allow for WP standard 'single' templates for compatibility.
		$templates[] = "single-{$post->post_type}.php";
		$templates[] = 'single.php';

		// Add a general template of singular.php.
		$templates[] = 'singular.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Fix for the front page template handling in WordPress core. Its handling is not logical because it
	 * forces devs to account for both a page on the front page and posts on the front page. Theme devs
	 * must handle both scenarios if they've created a "front-page.php" template. This filter overwrites
	 * that and disables the "front-page.php" template if posts are to be shown on the front page. This
	 * way, the "front-page.php" template will only ever be used if an actual page is supposed to be
	 * shown on the front.
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
	 * Overrides the default comments template. This filter allows for a "comments-{$post_type}.php"
	 * template based on the post type of the current single post view. If this template is not found, it falls
	 * back to the default "comments.php" template.
	 *
	 * @since  1.5.0
	 * @access public
	 * @param  string $template The comments template file name.
	 * @return string $template The theme comments template after all templates have been checked for.
	 */
	public function comments_template( $template ) {
		$templates = array();

		// Allow for custom templates entered into comments_template( $file ).
		$template = str_replace( trailingslashit( get_template_directory() ), '', $template );

		if ( 'comments.php' !== $template ) {
			$templates[] = $template;
		}

		// Add a comments template based on the post type.
		$templates[] = 'comments-' . get_post_type() . '.php';

		// Add the default comments template.
		$templates[] = 'comments.php';

		// Return the found template.
		return locate_template( $templates );
	}

	/**
	 * Gets a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function get_post_template( $post_id ) {
		return get_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ), true );
	}

	/**
	 * Sets a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @param  string  $template
	 * @return bool
	 */
	public function set_post_template( $post_id, $template ) {
		return update_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ), $template );
	}

	/**
	 * Deletes a post template.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function delete_post_template( $post_id ) {
		return delete_post_meta( $post_id, $this->get_post_template_meta_key( $post_id ) );
	}

	/**
	 * Checks if a post of any post type has a custom template. This is the equivalent of WordPress'
	 * `is_page_template()` function with the exception that it works for all post types.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $template  The name of the template to check for.
	 * @param  int     $post_id
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
	 * Returns the post template meta key.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return string
	 */
	public function get_post_template_meta_key( $post_id ) {
		return sprintf( '_wp_%s_template', get_post_type( $post_id ) );
	}

}
