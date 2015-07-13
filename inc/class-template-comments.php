<?php
/**
 * Methods for handling how comments are displayed and used on the site.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Template Tags Class.
 */
class CareLib_Template_Comments {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * An array of comment template paths.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	public static $comment_template = array();

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Return the comment reply link.
	 *
	 * Note that WP's `comment_reply_link()` doesn't work outside of
	 * `wp_list_comments()` without passing in the proper arguments.
	 *
	 * This function is just a wrapper for `get_comment_reply_link()`, which
	 * adds in the arguments automatically.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array  $args
	 * @return string
	 */
	public function get_reply_link( $args = array() ) {
		if ( ! get_option( 'thread_comments' ) || in_array( get_comment_type(), array( 'pingback', 'trackback' ) ) ) {
			return '';
		}

		$args = wp_parse_args(
			$args,
			array(
				'depth'     => intval( $GLOBALS['comment_depth'] ),
				'max_depth' => get_option( 'thread_comments_depth' ),
			)
		);

		return get_comment_reply_link( $args );
	}

	/**
	 * Uses the $comment_type to determine which comment template should be used.
	 * Once the template is located, it is loaded for use.
	 *
	 * Child themes can create custom templates based off the $comment_type. The
	 * comment template hierarchy is comment-$comment_type.php, comment.php.
	 *
	 * Templates are saved in CareLib_Template_Comments::comment_template[$comment_type],
	 * so each comment template is only located once if it is needed. The
	 * following comments will use the saved template.
	 *
	 * @since  0.2.3
	 * @access public
	 * @param  $comment object the comment object.
	 * @param  $args array list of arguments passed from wp_list_comments().
	 * @param  $depth integer What level the particular comment is.
	 * @return void
	 */
	public function comments_callback( $comment, $args, $depth ) {
		// Get the comment type of the current comment.
		$comment_type = get_comment_type( $comment->comment_ID );

		// Check if a template has been provided for the specific comment type. If not, get the template.
		if ( ! isset( self::$comment_template[ $comment_type ] ) ) {

			// Create an array of template files to look for.
			$templates = array(
				"comment-{$comment_type}.php",
				"comment/{$comment_type}.php",
			);

			// If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'.
			if ( 'pingback' === $comment_type || 'trackback' === $comment_type ) {
				$templates[] = 'comment-ping.php';
				$templates[] = 'comment/ping.php';
			}

			// Add the fallback 'comment.php' template.
			$templates[] = 'comment/comment.php';
			$templates[] = 'comment.php';

			// Allow devs to filter the template hierarchy.
			$templates = apply_filters( "{$this->prefix}_comment_template_hierarchy", $templates, $comment_type );

			// Locate the comment template.
			$template = locate_template( $templates );

			// Set the template in the comment template array.
			self::$comment_template[ $comment_type ] = $template;
		}

		// If a template was found, load the template.
		if ( ! empty( self::$comment_template[ $comment_type ] ) ) {
			require self::$comment_template[ $comment_type ];
		}
	}

}
