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

class CareLib_Template_Entry {
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
		$this->prefix = carelib()->get_prefix();
		$this->attr   = carelib_get( 'attributes' );
	}

	/**
	 * Protected helper function to format the entry title's display.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  mixed  $id the desired title's post id.
	 * @param  string $link the desired title's link URI.
	 * @return string
	 */
	protected function get_formatted_title( $id = '', $link = '' ) {
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
	 * Wrapper for get_the_title format the post title without needing to add a
	 * lot of extra markup in template files.
	 *
	 * By default, all entry titles except the main title on single entries are
	 * wrapped in an anchor tag pointed to the post's permalink.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $args Empty array if no arguments.
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

		// Bail if required args have been removed via a filter.
		if ( ! isset( $args['tag'], $args['post_id'], $args['attr'], $args['link'] ) ) {
			return false;
		}

		$html = '';

		$html .= isset( $args['before'] ) ? $args['before'] : '';

		$html .= sprintf( '<%1$s %2$s>%3$s</%1$s>',
			$args['tag'],
			$this->attr->get_attr( $args['attr'] ),
			$this->get_formatted_title( $args['post_id'], $args['link'] )
		);

		$html .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_title", $html, $args );
	}

	/**
	 * Get a post's published date and format it to be displayed in a template.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $args Empty array if no arguments.
	 * @return string
	 */
	public function get_entry_published( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_published_defaults",
			array(
				'attr'   => 'entry-published',
				'date'   => get_the_date(),
				'wrap'   => '<time %s>%s</time>',
				'before' => '',
				'after'  => '',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		// Bail if required args have been removed via a filter.
		if ( ! isset( $args['attr'], $args['date'], $args['wrap'] ) ) {
			return false;
		}

		$html = '';

		$html .= isset( $args['before'] ) ? $args['before'] : '';

		$html .= sprintf( $args['wrap'],
			$this->attr->get_attr( $args['attr'] ),
			$args['date']
		);

		$html .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_published", $html, $args );
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
	 * @param  array $args Empty array if no arguments.
	 * @return string output
	 */
	public function get_entry_comments_link( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_comments_link_defaults",
			array(
				'hide_if_off' => 'enabled',
				'more'        => __( '% Comments', 'carelib' ),
				'one'         => __( '1 Comment', 'carelib' ),
				'zero'        => __( 'Leave a Comment', 'carelib' ),
				'before'      => '',
				'after'       => '',
			)
		);
		$args = wp_parse_args( $args, $defaults );

		$required = isset(
			$args['hide_if_off'],
			$args['more'],
			$args['one'],
			$args['zero']
		);

		// Bail if required args have been removed via a filter or comments are closed.
		if ( ! $required || ! ( comments_open() && 'enabled' === $args['hide_if_off'] ) ) {
			return false;
		}

		$link = get_comments_link();
		$text = get_comments_number_text( $args['zero'], $args['one'], $args['more'] );
		$html = '';

		$html .= isset( $args['before'] ) ? $args['before'] : '';

		$html .= sprintf( '<span class="entry-comments-link"><a rel="nofollow" href="%s">%s</a></span>',
			$link,
			$text
		);

		$html .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_comments_link", $html, $link, $text, $args );
	}

	/**
	 * Backwards compatible wrapper for get_the_author_posts_link() which was
	 * added to WordPress core in 4.4.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	protected function get_the_author_posts_link() {
		if ( function_exists( 'get_the_author_posts_link' ) ) {
			return get_the_author_posts_link();
		}

		ob_start();
		the_author_posts_link();
		return ob_get_clean();
	}

	/**
	 * Get the current post's author in The Loop and optionally link to their
	 * archive page.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $args Empty array if no arguments.
	 * @return string
	 */
	public function get_entry_author( $args = array() ) {
		$defaults = apply_filters( "{$this->prefix}_entry_author_defaults",
			array(
				'text'   => '%s',
				'link'   => $this->get_the_author_posts_link(),
				'attr'   => 'entry-author',
				'wrap'   => '<span %s>%s</span>',
				'before' => '',
				'after'  => '',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		// Bail if required args have been removed via a filter.
		if ( ! isset( $args['text'], $args['link'], $args['attr'], $args['wrap'] ) ) {
			return false;
		}

		$html = '';

		$html .= isset( $args['before'] ) ? $args['before'] : '';

		$html .= sprintf( $args['wrap'],
			$this->attr->get_attr( $args['attr'] ),
			sprintf( $args['text'], $args['link'] )
		);

		$html .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_author", $html, $args );
	}

	/**
	 * Helper function to determine whether we should display the full content
	 * or an excerpt.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool true on singular entries by default
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
	 * Checks if a post has any content. Useful if you need to check if the user
	 * has written any content before performing any actions.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int $post_id The ID of the post to check for content.
	 * @return bool
	 */
	public function entry_has_content( $post_id = 0 ) {
		$post    = get_post( $post_id );
		$content = apply_filters( 'the_content', $post->post_content );
		return ! empty( $content );
	}

	/**
	 * Remove all actions from THA entry hooks.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function null_entry_containers() {
		remove_all_actions( 'tha_entry_top' );
		remove_all_actions( 'tha_entry_before' );
		remove_all_actions( 'tha_entry_content_before' );
		remove_all_actions( 'tha_entry_content_after' );
		remove_all_actions( 'tha_entry_bottom' );
		remove_all_actions( 'tha_entry_after' );
	}

	/**
	 * Filter the WordPress content to null between the entry_content_before
	 * and entrY_content_after hook locations.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function null_entry_content() {
		add_action( 'tha_entry_content_before',   array( $this, 'null_the_content' ), 99 );
		remove_action( 'tha_entry_content_after', array( $this, 'null_the_content' ),  5 );
	}

	/**
	 * Remove all actions from THA entry hooks and filter the WordPress post
	 * content to return null.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function null_entry() {
		$this->null_entry_containers();
		$this->null_entry_content();
	}

	/**
	 * Hookable wrapper around a filter to null the WordPress core post content.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function null_the_content() {
		add_filter( 'the_content', '__return_null' );
	}

	/**
	 * Replacement for template tags like `the_category()`, `the_terms()`, etc.
	 *
	 * These core WordPress template tags don't offer proper translation and
	 * RTL support without having to write a lot of messy code within templates.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $args Empty array if no arguments.
	 * @return string
	 */
	public function get_entry_terms( $args = array() ) {
		$defaults = array(
			'post_id'    => get_the_ID(),
			'taxonomy'   => 'category',
			'text'       => '%s',
			'wrap'       => '<span %s>%s</span>',
			// Translators: Separates tags, categories, etc. when displaying a post.
			'sep'        => _x( ', ', 'taxonomy terms separator', 'carelib' ),
			'before'     => '',
			'after'      => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$required = isset(
			$args['post_id'],
			$args['taxonomy'],
			$args['text'],
			$args['wrap'],
			$args['sep']
		);

		// Bail if required args have been removed via a filter.
		if ( ! $required ) {
			return false;
		}

		$terms = get_the_term_list(
			$args['post_id'],
			$args['taxonomy'],
			'',
			$args['sep'],
			''
		);

		if ( ! $terms ) {
			return false;
		}

		$html = '';

		$html .= isset( $args['before'] ) ? $args['before'] : '';

		$html .= sprintf( $args['wrap'],
			$this->attr->get_attr( 'entry-terms', $args['taxonomy'] ),
			sprintf( $args['text'], $terms )
		);

		$html .= isset( $args['after'] ) ? $args['after'] : '';

		return apply_filters( "{$this->prefix}_entry_terms", $html, $terms, $args );
	}

	/**
	 * Retrieves the singular name label for a given post object.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  object $object a post object to use for retrieving the name.
	 * @return mixed null if no object is provided, otherwise the label string
	 */
	protected function get_post_type_name( $object ) {
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
	 * @param  array $args Empty array if no arguments.
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
				'prev_text'      => __( 'Previous', 'carelib' ) . esc_html( $name ),
				'next_text'      => __( 'Next', 'carelib' ) . esc_html( $name ),
				'in_same_term'   => false,
				'excluded_terms' => '',
				'taxonomy'       => 'category',
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$types = (array) $args['post_types'];

		// Bail if we're not on a single entry. All post types except pages are allowed by default.
		if ( ! is_singular( $types ) || ( ! in_array( 'page', $types, true ) && is_page() ) ) {
			return false;
		}

		$required = isset(
			$args['prev_format'],
			$args['prev_text'],
			$args['next_format'],
			$args['next_text'],
			$args['in_same_term'],
			$args['excluded_terms'],
			$args['taxonomy']
		);

		// Bail if required args have been removed via a filter.
		if ( ! $required ) {
			return false;
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
			return false;
		}

		return sprintf( '<nav %s>%s</nav><!-- .nav-single -->',
			$this->attr->get_attr( 'nav', 'single' ),
			$links
		);
	}

	/**
	 * Gets a URL from the content, even if it's not wrapped in an <a> tag.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $content The content to search for links.
	 * @return string The content with links made clickable.
	 */
	public function get_content_url( $content ) {
		// Catch links that are not wrapped in an '<a>' tag.
		preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( $content ), $matches );
		return ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
	}
}
