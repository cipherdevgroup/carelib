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
function carelib_is_plural() {
	return apply_filters( "{$GLOBALS['carelib_prefix']}_is_plural", is_home() || is_archive() || is_search() );
}

/**
 * Determine if we're within a blog section archive.
 *
 * @since  0.1.1
 * @access public
 * @return bool true if we're on a blog archive page.
 */
function carelib_is_blog_archive() {
	return carelib_is_plural() && ! ( is_post_type_archive() || is_tax() );
}

/**
 * Determine whether or not to display an archive header.
 *
 * @since  0.2.0
 * @access public
 * @return bool
 */
function carelib_has_archive_header() {
	return (bool) apply_filters( "{$GLOBALS['carelib_prefix']}_has_archive_header", is_archive() || is_search() );
}

/**
 * Retrieve the general archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_archive_title() {
	return esc_html__( 'Archives', 'carelib' );
}

/**
 * Retrieve the author archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_author_title() {
	return get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );
}

/**
 * Retrieve the year archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_year_title() {
	return get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'carelib' ) );
}

/**
 * Retrieve the week archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_week_title() {
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
function carelib_get_single_day_title() {
	return get_the_date( esc_html_x( 'F j, Y', 'daily archives date format', 'carelib' ) );
}

/**
 * Retrieve the hour archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_hour_title() {
	return get_the_time( esc_html_x( 'g a', 'hour archives time format', 'carelib' ) );
}

/**
 * Retrieve the minute archive title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_single_minute_title() {

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
function carelib_get_single_minute_hour_title() {
	return get_the_time( esc_html_x( 'g:i a', 'minute and hour archives time format', 'carelib' ) );
}

/**
 * Retrieve the search results title.
 *
 * @since  0.2.0
 * @access public
 * @return string
 */
function carelib_get_search_title() {

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
function carelib_get_404_title() {
	return esc_html__( '404 Not Found', 'carelib' );
}

/**
 * Filters `get_the_archve_title` to add better archive titles than core.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $title
 * @return string
 */
function carelib_archive_title( $title ) {
	if ( is_home() && ! is_front_page() ) {
		$title = get_post_field( 'post_title', get_queried_object_id() );
	} elseif ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_author() ) {
		$title = carelib_get_single_author_title();
	} elseif ( is_search() ) {
		$title = carelib_get_search_title();
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( get_query_var( 'minute' ) && get_query_var( 'hour' ) ) {
		$title = carelib_get_single_minute_hour_title();
	} elseif ( get_query_var( 'minute' ) ) {
		$title = carelib_get_single_minute_title();
	} elseif ( get_query_var( 'hour' ) ) {
		$title = carelib_get_single_hour_title();
	} elseif ( is_day() ) {
		$title = carelib_get_single_day_title();
	} elseif ( get_query_var( 'w' ) ) {
		$title = carelib_get_single_week_title();
	} elseif ( is_month() ) {
		$title = single_month_title( ' ', false );
	} elseif ( is_year() ) {
		$title = carelib_get_single_year_title();
	} elseif ( is_archive() ) {
		$title = carelib_get_single_archive_title();
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_archive_title", $title );
}

/**
 * Filters `get_the_archve_description` to add better archive descriptions
 * than core.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $desc
 * @return string
 */
function carelib_archive_description( $desc ) {
	if ( is_home() && ! is_front_page() ) {
		$desc = get_post_field( 'post_content', get_queried_object_id(), 'raw' );
	}

	if ( is_category() ) {
		$desc = get_term_field( 'description', get_queried_object_id(), 'category', 'raw' );
	}

	if ( is_tag() ) {
		$desc = get_term_field( 'description', get_queried_object_id(), 'post_tag', 'raw' );
	}

	if ( is_tax() ) {
		$desc = get_term_field( 'description', get_queried_object_id(), get_query_var( 'taxonomy' ), 'raw' );
	}

	if ( is_author() ) {
		$desc = get_the_author_meta( 'description', get_query_var( 'author' ) );
	}

	if ( is_post_type_archive() ) {
		$desc = get_post_type_object( get_query_var( 'post_type' ) )->description;
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_archive_description", $desc );
}


/**
 * Helper function to build a newer/older or paginated navigation element within
 * a loop of multiple entries. This takes care of all the annoying formatting
 * which usually would need to be done within a template.
 *
 * @since  0.1.0
 * @access public
 * @param  array $args An optional list of options.
 * @return string
 */
function carelib_get_posts_navigation( $args = array() ) {
	global $wp_query;
	// Return early if we're on a singular post or we only have one page.
	if ( is_singular() || 1 === $wp_query->max_num_pages ) {
		return;
	}

	$defaults = apply_filters( "{$GLOBALS['carelib_prefix']}_posts_navigation_defaults",
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

	$output .= '<nav ' . carelib_get_attr( 'nav', 'archive' ) . '>';
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

	return apply_filters( "{$GLOBALS['carelib_prefix']}_posts_navigation", $output, $args );
}
