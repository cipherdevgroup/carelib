<?php
/**
 * Template helper functions used within archives.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class CareLib_Template_Archive {
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
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Determine if we're viewing a "plural" page.
	 *
	 * Note that this is similar to, but not quite the same as `!is_singular()`,
	 * which wouldn't account for the 404 page.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_plural() {
		return apply_filters( "{$this->prefix}_is_plural", is_home() || is_archive() || is_search() );
	}

	/**
	 * Determine if we're within a blog section archive.
	 *
	 * @since  0.1.1
	 * @access public
	 * @return bool true if we're on a blog archive page.
	 */
	public function is_blog_archive() {
		return $this->is_plural() && ! ( is_post_type_archive() || is_tax() );
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

		$output .= '<nav ' . carelib_get( 'attributes' )->get_attr( 'nav', 'archive' ) . '>';
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
}
