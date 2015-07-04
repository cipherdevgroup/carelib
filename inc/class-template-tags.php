<?php
/**
 * General template helper functions.
 *
 * @package    CareLib
 * @subpackage HybridCore
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Template Tags Class.
 */
class CareLib_Template_Tags {

	/**
	 * The library object.
	 *
	 * @since 0.1.0
	 * @type CareLib
	 */
	protected $lib;

	/**
	 * Filter prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->lib    = CareLib::instance();
		$this->prefix = $this->lib->get_prefix();
	}

	/**
	 * Retrieve the site logo URL or ID (URL by default). Pass in the string
	 * 'id' for ID.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::get_site_logo
	 * @param  string $format the format to return
	 * @return mixed The URL or ID of our site logo, false if not set
	 */
	public function get_logo( $format = 'url' ) {
		if ( class_exists( 'CareLib_Site_Logo', false ) ) {
			return CareLib_Factory::get( 'site-logo' )->get_site_logo( $format );
		}
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_get_site_logo( $format );
		}
	}

	/**
	 * Determine if a site logo is assigned or not.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::has_site_logo
	 * @return boolean True if there is an active logo, false otherwise
	 */
	public function has_logo() {
		if ( class_exists( 'CareLib_Site_Logo', false ) ) {
			return CareLib_Factory::get( 'site-logo' )->has_site_logo();
		}
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_has_site_logo();
		}
	}

	/**
	 * Output an <img> tag of the site logo, at the size specified
	 * in the theme's add_theme_support() declaration.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::the_site_logo
	 * @return void
	 */
	public function the_logo() {
		if ( class_exists( 'CareLib_Site_Logo', false ) ) {
			CareLib_Factory::get( 'site-logo' )->the_site_logo();
			return;
		}
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			jetpack_the_site_logo();
		}
	}

	/**
	 * Helper function to determine if we're within a blog section archive.
	 *
	 * @since  0.1.1
	 * @access public
	 * @return bool true if we're on a blog archive page.
	 */
	public function is_blog_archive() {
		return is_home() || is_archive() && ! ( is_post_type_archive() || is_tax() );
	}

	/**
	 * Display our breadcrumbs based on selections made in the WordPress customizer.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool true if both our template tag and theme mod return true.
	 */
	public function display_breadcrumbs() {
		$breadcrumbs = CareLib_Factory::get( 'breadcrumb-display' );
		// Return early if our theme doesn't support breadcrumbs.
		if ( ! is_object( $breadcrumbs ) ) {
			return false;
		}
		return $breadcrumbs->display_breadcrumbs();
	}

	/**
	 * This is a private helper function used to format the entry title's display.
	 * If a link is passed into it, the title will be wrapped in an anchor tag which
	 * links to the desired URI.
	 *
	 * This technically can be used by theme developers, but it isn't recommended as
	 * we make no guarantee of backwards compatibility in the future.
	 *
	 * @since  0.2.0
	 * @access private
	 * @param  $id mixed the desired title's post id
	 * @param  $link string the desired title's link URI
	 * @return string
	 */
	private function get_formatted_title( $id = '', $link = '' ) {
		$post_id = empty( $id ) ? get_the_ID() : $id;
		$title   = get_the_title( absint( $post_id ) );

		if ( empty( $link ) ) {
			return $title;
		}

		if ( get_permalink() === $link && get_the_ID() !== $post_id ) {
			$link = get_permalink( absint( $post_id ) );
		}

		return sprintf( '<a href="%s" rel="bookmark" itemprop="url">%s</a>',
			esc_url( $link ),
			$title
		);
	}

	/**
	 * This is a wrapper function for get_the_title which allows theme developers to
	 * grab a formatted version of the post title without needing to add a lot of
	 * extra markup in template files.
	 *
	 * By default, all entry titles except the main title on single entries are
	 * wrapped in an anchor tag pointed to the post's permalink.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  $args array
	 * @return string
	 */
	public function get_entry_title( $args = array() ) {
		$is_main  = is_singular() && is_main_query();
		$defaults = apply_filters( "{$this->prefix}_entry_title_defaults",
			array(
				'tag'     => $is_main ? 'h1' : 'h2',
				'attr'    => 'entry-title',
				'link'    => $is_main ? '' : get_permalink(),
				'post_id' => get_the_ID(),
				'before'  => '',
				'after'   => '',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$id   = isset( $args['post_id'] ) ? $args['post_id'] : '';
		$attr = isset( $args['attr'] ) ? hybrid_get_attr( $args['attr'] ) : '';
		$link = isset( $args['link'] ) ? $args['link'] : '';

		$output = isset( $args['before'] ) ? $args['before'] : '';

		$output .= sprintf( '<%1$s %2$s>%3$s</%1$s>',
			$args['tag'],
			$attr,
			$this->get_formatted_title( $id, $link )
		);

		$output .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_title", $output, $args );
	}

	/**
	 * This is simply a wrapper function for hybrid_get_post_author which adds a few
	 * filters to make the function a bit more flexible. This will allow us to avoid
	 * passing args into the function by default in our templates. Instead, we can
	 * filter the defaults globally which gives us a cleaner template file.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array
	 * @return string
	 */
	public function get_entry_author( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_author_defaults",
			array(
				'text'   => '%s',
				'before' => '',
				'after'  => '',
				'wrap'   => '<span %s>%s</span>',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		return apply_filters( "{$this->prefix}_entry_author", hybrid_get_post_author( $args ), $args );
	}

	/**
	 * Helper function for getting a post's published date and formatting it to be
	 * displayed in a template.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array
	 * @return string
	 */
	public function get_entry_published( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_published_defaults",
			array(
				'before' => '',
				'after'  => '',
				'attr'   => 'entry-published',
				'date'   => get_the_date(),
				'wrap'   => '<time %s>%s</time>',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$output  = isset( $args['before'] ) ? $args['before'] : '';
		$output .= sprintf( $args['wrap'], hybrid_get_attr( $args['attr'] ), $args['date'] );
		$output .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_published", $output, $args );
	}

	/**
	 * Produces a formatted link to the current entry comments.
	 *
	 * Supported arguments are:
	 * - after (output after link, default is empty string),
	 * - before (output before link, default is empty string),
	 * - hide_if_off (hide link if comments are off, default is 'enabled' (true)),
	 * - more (text when there is more than 1 comment, use % character as placeholder
	 *   for actual number, default is '% Comments')
	 * - one (text when there is exactly one comment, default is '1 Comment'),
	 * - zero (text when there are no comments, default is 'Leave a Comment').
	 *
	 * Output passes through 'carelib_get_entry_comments_link' filter before returning.
	 *
	 * @since  0.1.0
	 * @param  $args array Empty array if no arguments.
	 * @return string output
	 */
	public function get_entry_comments_link( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_comments_link_defaults",
			array(
				'after'       => '',
				'before'      => '',
				'hide_if_off' => 'enabled',
				'more'        => __( '% Comments', 'carelib' ),
				'one'         => __( '1 Comment', 'carelib' ),
				'zero'        => __( 'Leave a Comment', 'carelib' ),
			)
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! comments_open() && 'enabled' === $args['hide_if_off'] ) {
			return;
		}

		// I really would rather not do this, but WordPress is forcing me to.
		ob_start();
		comments_number( $args['zero'], $args['one'], $args['more'] );
		$comments = ob_get_clean();

		$comments = sprintf( '<a rel="nofollow" href="%s">%s</a>',
			get_comments_link(),
			$comments
		);

		$output  = isset( $args['before'] ) ? $args['before'] : '';
		$output .= '<span class="entry-comments-link">' . $comments . '</span>';
		$output .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_comments_link", $output, $args );
	}

	/**
	 * Helper function to determine whether we should display the full content
	 * or an excerpt.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return booleen true on singular entries by default
	 */
	protected function is_full_content() {
		return apply_filters( "{$this->prefix}_is_full_content", is_singular() );
	}

	/**
	 * Returns either an excerpt or the content depending on what page the user is
	 * currently viewing.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string the desired content
	 */
	public function get_content() {
		return apply_filters( 'the_content', $this->is_full_content() ? get_the_content() : get_the_excerpt() );
	}

	/**
	 * Retrieves the singular name label for a given post object.
	 *
	 * @since  0.2.0
	 * @access private
	 * @param  $object object a post object to use for retrieving the name
	 * @return mixed null if no object is provided, otherwise the label string
	 */
	private function get_post_type_name( $object ) {
		if ( ! is_object( $object ) ) {
			return null;
		}
		$obj = get_post_type_object( $object->post_type );
		return isset( $obj->labels->singular_name ) ? '&nbsp;' . $obj->labels->singular_name : null;
	}

	/**
	 * Helper function to build a next and previous post navigation element on
	 * single entries. This takes care of all the annoying formatting which usually
	 * would need to be done within a template.
	 *
	 * I originally wanted to use the new get_the_post_navigation tag for this;
	 * however, it's lacking a lot of the flexibility provided by using the old
	 * template tags directly. Until WordPress core gets its act together, I guess
	 * I'll just have to duplicate code for no good reason.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array
	 * @return string
	 */
	public function get_post_navigation( $args = array() ) {
		if ( is_attachment() || ! is_singular() ) {
			return;
		}

		$name = $this->get_post_type_name( get_queried_object() );

		$defaults = apply_filters( "{$this->prefix}_post_navigation_defaults",
			array(
				'post_types'     => array(),
				'prev_format'    => '<span class="nav-previous">%link</span>',
				'next_format'    => '<span class="nav-next">%link</span>',
				'prev_text'      => __( 'Previous', 'carelib' ) . esc_attr( $name ),
				'next_text'      => __( 'Next', 'carelib' ) . esc_attr( $name ),
				'in_same_term'   => false,
				'excluded_terms' => '',
				'taxonomy'       => 'category',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$types = (array) $args['post_types'];

		// Bail if we're not on a single entry. All post types except pages are allowed by default.
		if ( ! is_singular( $types ) || ( ! in_array( 'page', $types ) && is_page() ) ) {
			return;
		}

		$links = '';
		// Previous post link. Can be filtered via WP Core's previous_post_link filter.
		$links .= get_adjacent_post_link(
			$args['prev_format'],
			$args['prev_text'],
			$args['in_same_term'],
			$args['excluded_terms'],
			true,
			$args['taxonomy']
		);
		// Next post link. Can be filtered via WP Core's next_post_link filter.
		$links .= get_adjacent_post_link(
			$args['next_format'],
			$args['next_text'],
			$args['in_same_term'],
			$args['excluded_terms'],
			false,
			$args['taxonomy']
		);

		// Bail if we don't have any posts to link to.
		if ( empty( $links ) ) {
			return;
		}

		$output = '';

		$output .= '<nav ' . hybrid_get_attr( 'nav', 'single' ) . '>';
		$output .= $links;
		$output .= '</nav><!-- .nav-single -->';

		return $output;
	}

	/**
	 * Helper function to build a newer/older or paginated navigation element within
	 * a loop of multiple entries. This takes care of all the annoying formatting
	 * which usually would need to be done within a template.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array
	 * @return string
	 */
	public function get_posts_navigation( $args = array() ) {
		global $wp_query;
		// Return early if we're on a singular post or we only have one page.
		if ( is_singular() || 1 === $wp_query->max_num_pages ) {
			return;
		}

		$defaults = apply_filters( "{$this->prefix}_posts_navigation_defaults",
			array(
				'format'         => 'pagination',
				'prev_link_text' => __( 'Newer Posts', 'carelib' ),
				'next_link_text' => __( 'Older Posts', 'carelib' ),
				'prev_text'      => sprintf(
					'<span class="screen-reader-text">%s</span>' ,
					__( 'Previous Page', 'carelib' )
				),
				'next_text'      => sprintf(
					'<span class="screen-reader-text">%s</span>',
					__( 'Next Page', 'carelib' )
				),
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$output = '';

		$output .= '<nav ' . hybrid_get_attr( 'nav', 'archive' ) . '>';
		$output .= sprintf(
			'<span class="nav-previous">%s</span>',
			get_previous_posts_link( $args['prev_link_text'] )
		);

		$output .= sprintf(
			'<span class="nav-next">%s</span>',
			get_next_posts_link( $args['next_link_text'] )
		);

		$output .= '</nav><!-- .nav-archive -->';

		if ( function_exists( 'the_posts_pagination' ) && 'pagination' === $args['format'] ) {
			$output = get_the_posts_pagination(
				array(
					'prev_text' => $args['prev_text'],
					'next_text' => $args['next_text'],
				)
			);
		}

		return apply_filters( "{$this->prefix}_posts_navigation", $output, $args );
	}

	/**
	 * Format a link to the customizer panel.
	 *
	 * Since WordPress 4.1, the customizer panel allows for deeplinking, but setting
	 * up a link can be rather tedious. This function wraps the query args required
	 * to deep link to a customzer panel or control, plus return to the correct page
	 * when the customizer is exited by the user.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array options for how the link will be formatted
	 * @return string an escaped link to the WordPress customizer panel.
	 */
	public function get_customizer_link( $args = array() ) {
		$defaults = array(
			'focus_type'   => '',
			'focus_target' => '',
			'return'       => get_permalink(),
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array();
		$type       = $args['focus_type'];
		$target     = $args['focus_target'];
		$return     = $args['return'];

		if ( ! empty( $type ) && ! empty( $target ) ) {
			$query_args[] = array( 'autofocus' => array( $type => $target ) );
		}
		if ( ! empty( $return ) ) {
			$query_args['return'] = urlencode( wp_unslash( $return ) );
		}

		return esc_url( add_query_arg( $query_args, admin_url( 'customize.php' ) ) );
	}

	/**
	 * Returns a formatted theme credit link.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_credit_link() {
		$link = sprintf( '<a class="author-link" href="%s" title="%s">%s</a>',
			'http://www.wpsitecare.com',
			__( 'Free WordPress Theme by', 'carelib' ) . ' WP Site Care',
			'WP Site Care'
		);
		return apply_filters( "{$this->prefix}_credit_link", $link );
	}

	/**
	 * Returns formatted theme information.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_theme_info() {
		$info = '<div class="credit">';
		$info .= sprintf(
			// Translators: 1 is current year, 2 is site name/link, 3 is the theme author name/link.
			__( 'Copyright &#169; %1$s %2$s. Free WordPress Theme by %3$s', 'alpha' ),
			date_i18n( 'Y' ),
			hybrid_get_site_link(),
			$this->get_credit_link()
		);
		$info .= '</div>';
		return apply_filters( "{$this->prefix}_theme_info", $info );
	}
}
