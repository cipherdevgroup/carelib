<?php
/**
 * Methods for handling how comments are displayed and used on the site.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

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
	protected static $comment_template = array();

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
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
	 * @since  0.2.0
	 * @access public
	 * @param  object  $comment the comment object.
	 * @param  array   $args list of arguments passed from wp_list_comments().
	 * @param  integer $depth What level the particular comment is.
	 * @return void
	 */
	public function comments_callback( $comment, $args, $depth ) {
		// Get the comment type of the current comment.
		$comment_type = get_comment_type( $comment->comment_ID );

		// Check if a template has been provided for the specific comment type. If not, get the template.
		if ( ! isset( self::$comment_template[ $comment_type ] ) ) {

			// Create an array of template files to look for.
			$templates = array(
				"template-parts/comment/{$comment_type}.php",
				"comment-{$comment_type}.php",
			);

			// If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'.
			if ( 'pingback' === $comment_type || 'trackback' === $comment_type ) {
				$templates[] = 'template-parts/comment/ping.php';
				$templates[] = 'comment-ping.php';
			}

			// Add the fallback 'comment.php' template.
			$templates[] = 'template-parts/comment/comment.php';
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

	/**
	 * Output the comments template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function comments_template() {
		if ( apply_filters( "{$this->prefix}_display_comments", ! ( is_page() || is_attachment() ) ) ) {
			comments_template();
		}
	}
}
