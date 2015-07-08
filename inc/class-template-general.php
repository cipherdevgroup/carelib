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
class CareLib_Template_General {

	/**
	 * The library object.
	 *
	 * @since 0.1.0
	 * @type CareLib
	 */
	protected $lib;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	protected $attr;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->lib    = CareLib::instance();
		$this->prefix = $this->lib->get_prefix();
		$this->attr   = CareLib_Factory::get( 'attributes' );
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

		$output .= '<nav ' . $this->attr->get_attr( 'nav', 'single' ) . '>';
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

		$output .= '<nav ' . $this->attr->get_attr( 'nav', 'archive' ) . '>';
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
			carelib_get_site_link(),
			$this->get_credit_link()
		);
		$info .= '</div>';
		return apply_filters( "{$this->prefix}_theme_info", $info );
	}

	/**
	 * Returns a link back to the site.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_link() {
		return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>',
			esc_url( home_url() ),
			get_bloginfo( 'name' )
		);
	}

	/**
	 * Returns a link to WordPress.org.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_wp_link() {
		return sprintf( '<a class="wp-link" href="%s">%s</a>',
			esc_url( __( 'http://wordpress.org', 'carelib' ) ),
			esc_html__( 'WordPress', 'carelib' )
		);
	}

	/**
	 * Returns a link to the parent theme URI.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_theme_link() {
		$theme   = wp_get_theme( get_template() );
		$allowed = array(
			'abbr'    => array( 'title' => true ),
			'acronym' => array( 'title' => true ),
			'code'    => true,
			'em'      => true,
			'strong'  => true,
		);

		// Note: URI is escaped via `WP_Theme::markup_header()`.
		return sprintf( '<a class="theme-link" href="%s">%s</a>',
			$theme->display( 'ThemeURI' ),
			wp_kses( $theme->display( 'Name' ), $allowed )
		);
	}

	/**
	 * Returns a link to the child theme URI.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_child_theme_link() {
		if ( ! is_child_theme() ) {
			return '';
		}

		$theme   = wp_get_theme();
		$allowed = array(
			'abbr'    => array( 'title' => true ),
			'acronym' => array( 'title' => true ),
			'code'    => true,
			'em'      => true,
			'strong'  => true,
		);

		// Note: URI is escaped via `WP_Theme::markup_header()`.
		return sprintf( '<a class="child-link" href="%s">%s</a>',
			$theme->display( 'ThemeURI' ),
			wp_kses( $theme->display( 'Name' ),	$allowed )
		);
	}

	/**
	 * Gets the "blog" (posts page) page URL. `home_url()` will not always work for this because it
	 * returns the front page URL. Sometimes the blog page URL is set to a different page. This
	 * function handles both scenarios.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_blog_url() {
		if ( 'posts' === get_option( 'show_on_front' ) ) {
			$blog_url = home_url();
		}

		if ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) ) {
			$blog_url = get_permalink( $page_for_posts );
		}

		return empty( $blog_url ) ? '' : esc_url( $blog_url );
	}

	/**
	 * Returns the linked site title wrapped in an `<h1>` tag.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_title() {
		if ( $title = get_bloginfo( 'name' ) ) {
			$title = sprintf( '<%1$s %2$s><a href="%2$s" rel="home">%4$s</a></%1$s>',
				is_front_page() || is_home() ? 'h1' : 'p',
				$this->attr->get_attr( 'site-title' ),
				esc_url( home_url() ),
				$title
			);
		}

		return apply_filters( 'carelib_site_title', $title );
	}

	/**
	 * Returns the site description wrapped in an `<h2>` tag.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_description() {
		if ( $desc = get_bloginfo( 'description' ) ) {
			$desc = sprintf( '<p %s>%s</p>',
				$this->attr->get_attr( 'site-description' ),
				$desc
			);
		}

		return apply_filters( 'carelib_site_description', $desc );
	}

	/**
	 * Function for figuring out if we're viewing a "plural" page. In WP, these pages are archives,
	 * search results, and the home/blog posts index. Note that this is similar to, but not quite
	 * the same as `!is_singular()`, which wouldn't account for the 404 page.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_plural() {
		return apply_filters( 'carelib_is_plural', is_home() || is_archive() || is_search() );
	}

	/**
	 * Retrieve the general archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_archive_title() {
		return esc_html__( 'Archives', 'carelib' );
	}

	/**
	 * Retrieve the author archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function get_single_author_title() {
		return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
	}

	/**
	 * Retrieve the year archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_year_title() {
		return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'carelib' ) );
	}

	/**
	 * Retrieve the week archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_week_title() {
		// Translators: 1 is the week number and 2 is the year.
		return sprintf(
			esc_html__( 'Week %1$s of %2$s', 'carelib' ),
			get_the_time( esc_html_x( 'W', 'weekly archives date format', 'carelib' ) ),
			get_the_time( esc_html_x( 'Y', 'yearly archives date format', 'carelib' ) )
		);
	}

	/**
	 * Retrieve the day archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_day_title() {
		return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'carelib' ) );
	}

	/**
	 * Retrieve the hour archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_hour_title() {
		return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'carelib' ) );
	}

	/**
	 * Retrieve the minute archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_minute_title() {

		// Translators: Minute archive title. %s is the minute time format.
		return sprintf( esc_html__( 'Minute %s', 'carelib' ), get_the_time( esc_html_x( 'i', 'minute archives time format', 'carelib' ) ) );
	}

	/**
	 * Retrieve the minute + hour archive title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_single_minute_hour_title() {
		return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'carelib' ) );
	}

	/**
	 * Retrieve the search results title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_search_title() {

		// Translators: %s is the search query. The HTML entities are opening and closing curly quotes.
		return sprintf( esc_html__( 'Search results for &#8220;%s&#8221;', 'carelib' ), get_search_query() );
	}

	/**
	 * Retrieve the 404 page title.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_404_title() {
		return esc_html__( '404 Not Found', 'carelib' );
	}

}
