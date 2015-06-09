<?php
/**
 * Helper functions for entry templates.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * This is a "private" helper function used to format the entry title's display.
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
function _sitecare_get_formatted_title( $id = '', $link = '' ) {
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
function sitecare_get_entry_title( $args = array() ) {
	$is_main  = is_singular() && is_main_query();
	$defaults = apply_filters( 'sitecare_entry_title_defaults',
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
		_sitecare_get_formatted_title( $id, $link )
	);

	$output .= isset( $args['after'] ) ? $args['after'] : '';

	return apply_filters( 'sitecare_entry_title', $output, $args );
}

/**
 * Outputs a formatted entry title.
 *
 * @since  0.2.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_entry_title( $args = array() ) {
	echo sitecare_get_entry_title( $args );
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
function sitecare_get_entry_author( $args = array() ) {
	$defaults = apply_filters( 'sitecare_entry_author_defaults',
		array(
			'text'   => '%s',
			'before' => '',
			'after'  => '',
			'wrap'   => '<span %s>%s</span>',
		)
	);

	$args = wp_parse_args( $args, $defaults );

	return apply_filters( 'sitecare_entry_author', hybrid_get_post_author( $args ), $args );
}

/**
 * Outputs an entry's author.
 *
 * @since  0.1.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_entry_author( $args = array() ) {
	echo sitecare_get_entry_author( $args );
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
function sitecare_get_entry_published( $args = array() ) {
	$defaults = apply_filters( 'sitecare_entry_published_defaults',
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

	return apply_filters( 'sitecare_entry_published', $output, $args );
}

/**
 * Outputs a post's published date.
 *
 * @since  0.1.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_entry_published( $args = array() ) {
	echo sitecare_get_entry_published( $args );
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
 * Output passes through 'sitecare_get_entry_comments_link' filter before returning.
 *
 * @since  0.1.0
 * @param  $args array Empty array if no arguments.
 * @return string output
 */
function sitecare_get_entry_comments_link( $args = array() ) {
	$defaults = apply_filters( 'sitecare_entry_comments_link_defaults',
		array(
			'after'       => '',
			'before'      => '',
			'hide_if_off' => 'enabled',
			'more'        => __( '% Comments', 'sitecare-library' ),
			'one'         => __( '1 Comment', 'sitecare-library' ),
			'zero'        => __( 'Leave a Comment', 'sitecare-library' ),
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

	return apply_filters( 'sitecare_entry_comments_link', $output, $args );
}

/**
 * Displays a formatted link to the current entry comments.
 *
 * @since  0.1.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_entry_comments_link( $args = array() ) {
	echo sitecare_get_entry_comments_link( $args );
}

/**
 * Returns either an excerpt or the content depending on what page the user is
 * currently viewing.
 *
 * @since  0.2.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_get_content() {
	return apply_filters( 'sitecare_content', is_singular() ? get_the_content() : get_the_excerpt() );
}

/**
 * Displays either an excerpt or the content depending on what page the user is
 * currently viewing.
 *
 * @since  0.2.0
 * @access public
 * @param  $args array
 * @return void
 */
function sitecare_content() {
	echo sitecare_get_content();
}
