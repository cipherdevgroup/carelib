<?php
/**
 * General template helper functions.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.2.0
 */

/**
 * SiteCare Template Tags Class.
 */
class SiteCare_Template_Tags {

	/**
	 * Retrieve the site logo URL or ID (URL by default). Pass in the string
	 * 'id' for ID.
	 *
	 * @since  0.1.0
	 * @uses   SiteCare_Site_Logo::get_sitecare_logo
	 * @param  string $format the format to return
	 * @return mixed The URL or ID of our site logo, false if not set
	 */
	public function get_logo( $format = 'url' ) {
		if ( ! class_exists( 'SiteCare_Site_Logo', false ) ) {
			if ( function_exists( 'jetpack_the_site_logo' ) ) {
				return jetpack_get_site_logo( $format );
			}
			if ( function_exists( 'the_site_logo' ) ) {
				return get_site_logo( $format );
			}
			return null;
		}
		return sitecare_library()->site_logo->get_sitecare_logo( $format );
	}

	/**
	 * Determine if a site logo is assigned or not.
	 *
	 * @since  0.1.0
	 * @uses   SiteCare_Site_Logo::has_site_logo
	 * @return boolean True if there is an active logo, false otherwise
	 */
	public function has_logo() {
		if ( ! class_exists( 'SiteCare_Site_Logo', false ) ) {
			if ( function_exists( 'jetpack_the_site_logo' ) ) {
				return jetpack_has_site_logo();
			}
			if ( function_exists( 'the_site_logo' ) ) {
				return has_site_logo();
			}
			return null;
		}
		return sitecare_library()->site_logo->has_site_logo();
	}

	/**
	 * Output an <img> tag of the site logo, at the size specified
	 * in the theme's add_theme_support() declaration.
	 *
	 * @since  0.1.0
	 * @uses   SiteCare_Site_Logo::the_site_logo
	 * @return void
	 */
	public function the_logo() {
		if ( ! class_exists( 'SiteCare_Site_Logo', false ) ) {
			if ( function_exists( 'jetpack_the_site_logo' ) ) {
				jetpack_the_site_logo();
				return;
			}
			if ( function_exists( 'the_site_logo' ) ) {
				the_site_logo();
				return;
			}
			return;
		}
		sitecare_library()->site_logo->the_site_logo();
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
	 * Helper function to determine if we're anywhere within the blog section.
	 *
	 * @since  0.1.1
	 * @access public
	 * @return bool true if we're on a blog archive page or a singular post.
	 */
	public function is_blog() {
		return $this->is_blog_archive() || is_singular( 'post' );
	}

	/**
	 * Display our breadcrumbs based on selections made in the WordPress customizer.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool true if both our template tag and theme mod return true.
	 */
	public function display_breadcrumbs() {
		$breadcrumbs = sitecare_library()->breadcrumb_display;
		// Return early if our theme doesn't support breadcrumbs.
		if ( ! is_object( $breadcrumbs ) ) {
			return false;
		}
		return $breadcrumbs->display_breadcrumbs();
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
		if ( is_attachment() ) {
			return;
		}
		$obj  = get_post_type_object( get_post_type() );
		$name = isset( $obj->labels->singular_name ) ? '&nbsp;' . $obj->labels->singular_name : '';

		$defaults = apply_filters( 'sitecare_post_navigation_defaults',
			array(
				'post_types'     => array(),
				'prev_format'    => '<span class="nav-previous">%link</span>',
				'next_format'    => '<span class="nav-next">%link</span>',
				'prev_text'      => __( 'Previous', 'sitecare-library' ) . esc_attr( $name ),
				'next_text'      => __( 'Next', 'sitecare-library' ) . esc_attr( $name ),
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

		$defaults = apply_filters( 'sitecare_posts_navigation_defaults',
			array(
				'format'         => 'pagination',
				'prev_text'      => sprintf( '<span class="screen-reader-text">%s</span>' , __( 'Previous Page', 'sitecare-library' ) ),
				'next_text'      => sprintf( '<span class="screen-reader-text">%s</span>', __( 'Next Page', 'sitecare-library' ) ),
				'prev_link_text' => __( 'Newer Posts', 'sitecare-library' ),
				'next_link_text' => __( 'Older Posts', 'sitecare-library' ),
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$output = '';

		$output .= '<nav ' . hybrid_get_attr( 'nav', 'archive' ) . '>';
		$output .= sprintf( '<span class="nav-previous">%s</span>', get_previous_posts_link( $args['prev_link_text'] ) );
		$output .= sprintf( '<span class="nav-next">%s</span>', get_next_posts_link( $args['next_link_text'] ) );
		$output .= '</nav><!-- .nav-archive -->';

		if ( function_exists( 'the_posts_pagination' ) && 'pagination' === $args['format'] ) {
			$output = get_the_posts_pagination(
				array(
					'prev_text' => $args['prev_text'],
					'next_text' => $args['next_text'],
				)
			);
		}

		return apply_filters( 'sitecare_posts_navigation', $output, $args );
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
			__( 'Free WordPress Theme by', 'sitecare-library' ) . ' WP Site Care',
			'WP Site Care'
		);
		return apply_filters( 'sitecare_credit_link', $link );
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
			sitecare_get_credit_link()
		);
		$info .= '</div>';
		return apply_filters( 'sitecare_theme_info', $info );
	}
}