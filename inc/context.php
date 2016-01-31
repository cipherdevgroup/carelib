<?php
/**
 * Contextual functions and filters, particularly dealing with the body, post,
 * and comment classes.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib's main contextual function.
 *
 * This allows code to be used more than once without running hundreds of
 * conditional checks within the theme. It returns an array of contexts
 * based on what page a visitor is currently viewing on the site.
 *
 * @since  0.2.0
 * @access protected
 * @return array
 */
function _carelib_get_context() {
	$context   = array();
	$object    = get_queried_object();
	$object_id = get_queried_object_id();

	if ( is_front_page() ) {
		$context[] = 'home';
	} elseif ( is_home() ) {
		$context[] = 'blog';
	}

	if ( is_singular() ) {
		$context[] = 'singular';
		$context[] = "singular-{$object->post_type}";
		$context[] = "singular-{$object->post_type}-{$object_id}";
	} elseif ( is_archive() ) {
		$context[] = 'archive';

		if ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );

			if ( is_array( $post_type ) ) {
				reset( $post_type );
			}

			$context[] = "archive-{$post_type}";
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$context[] = 'taxonomy';
			$context[] = "taxonomy-{$object->taxonomy}";
			$context[] = "taxonomy-{$object->taxonomy}-" . sanitize_html_class( $object->slug, $object->term_id );
		} elseif ( is_author() ) {
			$user_id = get_query_var( 'author' );
			$context[] = 'user';
			$context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $user_id ), $user_id );
		} elseif ( is_date() ) {
			$context[] = 'date';

			if ( is_year() ) {
				$context[] = 'year';
			} elseif ( is_month() ) {
				$context[] = 'month';
			} elseif ( get_query_var( 'w' ) ) {
				$context[] = 'week';
			} elseif ( is_day() ) {
				$context[] = 'day';
			}
		} elseif ( is_time() ) {
			$context[] = 'time';

			if ( get_query_var( 'hour' ) ) {
				$context[] = 'hour';
			} elseif ( get_query_var( 'minute' ) ) {
				$context[] = 'minute';
			}
		}
	} elseif ( is_search() ) {
		$context[] = 'search';
	} elseif ( is_404() ) {
		$context[] = 'error-404';
	}

	return array_map( 'esc_attr', apply_filters( 'context', array_unique( $context ) ) );
}

/**
 * Filter the WordPress body class with a better set of default classes.
 *
 * The goal of this is to create classes which are more consistently handled
 * and are backwards compatible with the original body class functionality
 * that existed prior to WordPress core adopting this feature.
 *
 * @since  0.2.0
 * @access public
 * @param  array        $classes
 * @param  string|array $class
 * @return array
 */
function carelib_body_class_filter( $classes, $class ) {
	// WordPress class for uses when WordPress isn't always the only system on the site.
	$classes = array( 'wordpress' );

	// Text direction.
	$classes[] = is_rtl() ? 'rtl' : 'ltr';

	// Locale and language.
	$locale = get_locale();
	$lang   = carelib_get_language( $locale );

	if ( $locale !== $lang ) {
		$classes[] = $lang;
	}

	$classes[] = strtolower( str_replace( '_', '-', $locale ) );

	// Check if the current theme is a parent or child theme.
	$classes[] = is_child_theme() ? 'child-theme' : 'parent-theme';

	// Multisite check adds the 'multisite' class and the blog ID.
	if ( is_multisite() ) {
		$classes[] = 'multisite';
		$classes[] = 'blog-' . get_current_blog_id();
	}

	// Date classes.
	$time = time() + ( get_option( 'gmt_offset' ) * 3600 );
	$classes[] = strtolower( gmdate( '\yY \mm \dd \hH l', $time ) );

	// Is the current user logged in.
	$classes[] = is_user_logged_in() ? 'logged-in' : 'logged-out';

	// WP admin bar.
	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
	}

	// Use the '.custom-background' class to integrate with the WP background feature.
	if ( get_background_image() || get_background_color() ) {
		$classes[] = 'custom-background';
	}

	// Add the '.custom-header' class if the user is using a custom header.
	if ( get_header_image() || ( display_header_text() && get_header_textcolor() ) ) {
		$classes[] = 'custom-header';
	}

	// Add the '.display-header-text' class if the user chose to display it.
	if ( display_header_text() ) {
		$classes[] = 'display-header-text';
	}

	// Plural/multiple-post view (opposite of singular).
	if ( carelib_is_plural() ) {
		$classes[] = 'plural';
	}

	// Merge base contextual classes with $classes.
	$classes = array_merge( $classes, _carelib_get_context() );

	// Singular post (post_type) classes.
	if ( is_singular() ) {

		// Get the queried post object.
		$post = get_queried_object();

		// Checks for custom template.
		$template = str_replace(
			array(
				"{$post->post_type}-template-",
				"{$post->post_type}-",
			),
			'',
			basename( carelib_get_post_template( $post->ID ), '.php' )
		);
		if ( $template ) {
			$classes[] = "{$post->post_type}-template-{$template}";
		}

		// Attachment mime types.
		if ( is_attachment() ) {
			foreach ( explode( '/', get_post_mime_type() ) as $type ) {
				$classes[] = "attachment-{$type}";
			}
		}
	}

	// Paged views.
	if ( is_paged() || is_singular() && 1 < get_query_var( 'page' ) ) {
		$classes[] = 'paged';
		$classes[] = 'paged-' . intval( get_query_var( 'paged' ) );
	}

	// Theme layouts.
	if ( carelib_has_layout_support() ) {
		$classes[] = sanitize_html_class( 'layout-' . carelib_get_theme_layout() );
	}

	// Input class.
	if ( ! empty( $class ) ) {
		$class   = is_array( $class ) ? $class : preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	return array_map( 'esc_attr', $classes );
}

/**
 * Filter the WordPress post class with a better set of default classes.
 *
 * @since  0.2.0
 * @access public
 * @param  array        $classes
 * @param  string|array $class
 * @param  int          $post_id
 * @return array
 */
function carelib_post_class_filter( $classes, $class, $post_id ) {
	$_classes    = array();
	$post        = get_post( $post_id );
	$post_type   = get_post_type();
	$post_status = get_post_status();

	$remove = array( 'hentry', "type-{$post_type}", "status-{$post_status}", 'post-password-required' );

	foreach ( $classes as $key => $class ) {

		if ( in_array( $class, $remove, true ) ) {
			unset( $classes[ $key ] );
		} else {
			$classes[ $key ] = str_replace( 'tag-', 'post_tag-', $class );
		}
	}

	$_classes[] = 'entry';
	$_classes[] = $post_type;
	$_classes[] = $post_status;

	// Author class.
	$_classes[] = 'author-' . sanitize_html_class( get_the_author_meta( 'user_nicename' ), get_the_author_meta( 'ID' ) );

	// Password-protected posts.
	if ( post_password_required() ) {
		$_classes[] = 'protected';
	}

	// Has excerpt.
	if ( post_type_supports( $post->post_type, 'excerpt' ) && has_excerpt() ) {
		$_classes[] = 'has-excerpt';
	}

	// Has <!--more--> link.
	if ( ! is_singular() && false !== strpos( $post->post_content, '<!--more' ) ) {
		$_classes[] = 'has-more-link';
	}

	// Has <!--nextpage--> links.
	if ( false !== strpos( $post->post_content, '<!--nextpage' ) ) {
		$_classes[] = 'has-pages';
	}

	$_classes = array_map( 'esc_attr', $_classes );

	return array_unique( array_merge( $_classes, $classes ) );
}

/**
 * Adds custom classes to the WordPress comment class.
 *
 * @since  0.2.0
 * @access public
 * @param  array        $classes
 * @param  string|array $class
 * @param  int          $comment_id
 * @return array
 */
function carelib_comment_class_filter( $classes, $class, $comment_id ) {

	$comment = get_comment( $comment_id );

	// If the comment type is 'pingback' or 'trackback', add the 'ping' comment class.
	if ( in_array( $comment->comment_type, array( 'pingback', 'trackback' ), true ) ) {
		$classes[] = 'ping';
	}

	// User classes to match user role and user.
	if ( 0 < $comment->user_id ) {

		// Create new user object.
		$user = new WP_User( $comment->user_id );

		// Set a class with the user's role(s).
		if ( is_array( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				$classes[] = sanitize_html_class( "role-{$role}" );
			}
		}
	}

	// Get comment types that are allowed to have an avatar.
	$avatar_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );

	// If avatars are enabled and the comment types can display avatars, add the 'has-avatar' class.
	if ( get_option( 'show_avatars' ) && in_array( $comment->comment_type, $avatar_types, true ) ) {
		$classes[] = 'has-avatar';
	}

	return array_map( 'esc_attr', array_unique( $classes ) );
}
