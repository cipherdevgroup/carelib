<?php
/**
 * Template functions related to posts.
 *
 * The functions in this file are for handling template tags or features of
 * template tags that WordPress core does not currently handle.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Template Post Class.
 */
class CareLib_Template_Entry {

	/**
	 * The library object.
	 *
	 * @since 0.2.0
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

	/**
	 * The CareLib attributes class.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Attributes
	 */
	protected $attr;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->lib    = carelib();
		$this->prefix = $this->lib->get_prefix();
		$this->attr   = carelib_class( 'attributes' );
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
		$attr = isset( $args['attr'] ) ? $this->attr->get_attr( $args['attr'] ) : '';
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
		$output .= sprintf( $args['wrap'], $this->attr->get_attr( $args['attr'] ), $args['date'] );
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
	 * Output passes through "{$this->prefix}_get_entry_comments_link" filter before returning.
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
	 * Checks if a post has any content. Useful if you need to check if the user has written any content
	 * before performing any actions.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  int    $post_id
	 * @return bool
	 */
	public function entry_has_content( $post_id = 0 ) {
		$post = get_post( $post_id );
		return ! empty( $post->post_content );
	}

	/**
	 * Function for getting the current post's author in The Loop and linking to the author archive page.
	 * This function was created because core WordPress does not have template tags with proper translation
	 * and RTL support for this. An equivalent getter function for `the_author_posts_link()` would
	 * instantly solve this issue.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $args
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

		$html = '';

		// Output buffering to get the author posts link.
		ob_start();
		the_author_posts_link();
		$link = ob_get_clean();

		if ( $link ) {
			$html .= $args['before'];
			$html .= sprintf( $args['wrap'], $this->attr->get_attr( 'entry-author' ), sprintf( $args['text'], $link ) );
			$html .= $args['after'];
		}

		return apply_filters( "{$this->prefix}_entry_author", $html, $args );
	}

	/**
	 * This template tag is meant to replace template tags like `the_category()`, `the_terms()`, etc. These core
	 * WordPress template tags don't offer proper translation and RTL support without having to write a lot of
	 * messy code within the theme's templates. This is why theme developers often have to resort to custom
	 * functions to handle this (even the default WordPress themes do this). Particularly, the core functions
	 * don't allow for theme developers to add the terms as placeholders in the accompanying text (ex: "Posted in %s").
	 * This funcion is a wrapper for the WordPress `get_the_terms_list()` function. It uses that to build a
	 * better post terms list.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $args
	 * @return string
	 */
	public function get_entry_terms( $args = array() ) {
		$html = '';

		$defaults = array(
			'post_id'    => get_the_ID(),
			'taxonomy'   => 'category',
			'text'       => '%s',
			'before'     => '',
			'after'      => '',
			'wrap'       => '<span %s>%s</span>',
			// Translators: Separates tags, categories, etc. when displaying a post.
			'sep'        => _x( ', ', 'taxonomy terms separator', 'carelib' ),
		);

		$args = wp_parse_args( $args, $defaults );

		$terms = get_the_term_list( $args['post_id'], $args['taxonomy'], '', $args['sep'], '' );

		if ( $terms ) {
			$html .= $args['before'];
			$html .= sprintf( $args['wrap'], $this->attr->get_attr( 'entry-terms', $args['taxonomy'] ), sprintf( $args['text'], $terms ) );
			$html .= $args['after'];
		}

		return $html;
	}

	/**
	 * Gets the gallery *item* count. This is different from getting the gallery *image* count. By default,
	 * WordPress only allows attachments with the 'image' mime type in galleries. However, some scripts such
	 * as Cleaner Gallery allow for other mime types. This is a more accurate count than the
	 * carelib_get_gallery_image_count() function since it will count all gallery items regardless of mime type.
	 *
	 * @todo Check for the [gallery] shortcode with the 'mime_type' parameter and use that in get_posts().
	 *
	 * @since  1.6.0
	 * @access public
	 * @return int
	 */
	public function get_gallery_item_count() {
		// Check the post content for galleries.
		$galleries = get_post_galleries( get_the_ID(), true );

		// If galleries were found in the content, get the gallery item count.
		if ( ! empty( $galleries ) ) {
			$items = '';

			foreach ( $galleries as $gallery => $gallery_items ) {
				$items .= $gallery_items;
			}

			preg_match_all( '#src=([\'"])(.+?)\1#is', $items, $sources, PREG_SET_ORDER );

			if ( ! empty( $sources ) ) {
				return count( $sources );
			}
		}

		// If an item count wasn't returned, get the post attachments.
		$attachments = get_posts(
			array(
				'fields'         => 'ids',
				'post_parent'    => get_the_ID(),
				'post_type'      => 'attachment',
				'numberposts'    => -1,
			)
		);

		// Return the attachment count if items were found.
		return ! empty( $attachments ) ? count( $attachments ) : 0;
	}

	/**
	 * Returns the number of images displayed by the gallery or galleries in a post.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return int
	 */
	public function get_gallery_image_count() {
		// Set up an empty array for images.
		$images = array();

		// Get the images from all post galleries.
		$galleries = get_post_galleries_images();

		// Merge each gallery image into a single array.
		foreach ( $galleries as $gallery_images ) {
			$images = array_merge( $images, $gallery_images );
		}

		// If there are no images in the array, just grab the attached images.
		if ( empty( $images ) ) {
			$images = get_posts(
				array(
					'fields'         => 'ids',
					'post_parent'    => get_the_ID(),
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'numberposts'    => -1,
				)
			);
		}

		// Return the count of the images.
		return count( $images );
	}

	/**
	 * Gets a URL from the content, even if it's not wrapped in an <a> tag.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  string  $content
	 * @return string
	 */
	public function get_content_url( $content ) {
		// Catch links that are not wrapped in an '<a>' tag.
		preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( $content ), $matches );
		return ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
	}

}
