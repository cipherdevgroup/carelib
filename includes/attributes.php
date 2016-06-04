<?php
/**
 * HTML attribute functions and filters.
 *
 * The purposes of this is to provide a way for theme/plugin devs to hook into
 * the attributes for specific HTML elements and create new or modify existing
 * attributes.
 *
 * This is sort of like `body_class()`, `post_class()`, and `comment_class()` on
 * steroids. Plus, it handles attributes for many more elements. The biggest
 * benefit of using this is to provide richer microdata while being forward
 * compatible with the ever-changing Web. Currently, the default microdata
 * vocabulary supported is Schema.org.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Get an HTML element's attributes.
 *
 * This function is actually meant to be filtered by theme authors, plugins,
 * or advanced child theme users. The purpose is to allow folks to modify,
 * remove, or add any attributes they want without having to edit every
 * template file in the theme. So, one could support microformats instead
 * of microdata, if desired.
 *
 * @since  1.0.0
 * @access public
 * @param  string $slug The slug/ID of the element (e.g., 'sidebar').
 * @param  string $context A specific context (e.g., 'primary').
 * @param  array  $attr A list of attributes passed-in directly.
 * @return string
 */
function carelib_get_attr( $slug, $context = '', $attr = array() ) {
	$out   = '';
	$class = sanitize_html_class( $slug );

	if ( ! empty( $context ) ) {
		$class = "{$class} {$class}-" . sanitize_html_class( $context );
	}

	$attr = array_merge(
		array(
			'class' => $class,
		),
		(array) $attr
	);

	$attr = array_merge(
		$attr,
		(array) apply_filters( "{$GLOBALS['carelib_prefix']}_attr_{$slug}", $attr, $context )
	);

	foreach ( $attr as $name => $value ) {
		$out .= ! empty( $value ) ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
	}

	return trim( $out );
}

/**
 * <body> element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_body( $attr ) {
	$attr['class']     = join( ' ', get_body_class() );
	$attr['dir']       = is_rtl() ? 'rtl' : 'ltr';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPage';

	if ( is_search() ) {
		$attr['itemtype'] = 'http://schema.org/SearchResultsPage';
	}

	return $attr;
}

/**
 * Page <header> element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_header( $attr ) {
	$attr['id']        = 'site-header';
	$attr['role']      = 'banner';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPHeader';

	return $attr;
}

/**
 * Page site container element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_container( $attr ) {
	$attr['id'] = 'site-container';

	return $attr;
}

/**
 * Page site inner element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_inner( $attr ) {
	$attr['id'] = 'site-inner';

	return $attr;
}

/**
 * Page <footer> element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_footer( $attr ) {
	$attr['id']        = 'site-footer';
	$attr['role']      = 'contentinfo';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPFooter';

	return $attr;
}

/**
 * Main content container of the page attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_content( $attr ) {
	$attr['id']   = 'content';
	$attr['role'] = 'main';

	if ( ! is_singular( 'post' ) && ! is_home() && ! is_archive() ) {
		$attr['itemprop'] = 'mainContentOfPage';
	}

	return $attr;
}

/**
 * Skip link element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_skip_link( $attr, $context ) {
	if ( ! empty( $context ) ) {
		$attr['id'] = 'skip-link-' . sanitize_html_class( $context );
	}

	return $attr;
}

/**
 * Sidebar attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_sidebar( $attr, $context ) {
	$attr['role'] = 'complementary';

	if ( ! empty( $context ) ) {

		$attr['class'] .= " sidebar-{$context}";
		$attr['id']     = "sidebar-{$context}";

		if ( $name = carelib_get_sidebar_name( $context ) ) {
			// Translators: The %s is the sidebar name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Sidebar', 'sidebar aria label', 'carelib' ), $name ) );
		}
	}

	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WPSideBar';

	return $attr;
}

/**
 * Button attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_button( $attr, $context ) {
	$attr['role']          = 'button';
	$attr['aria-expanded'] = 'false';
	$attr['aria-pressed']  = 'false';

	if ( ! empty( $context ) ) {
		$attr['id'] = "button-{$context}";
	}

	return $attr;
}

/**
 * Menu toggle attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_menu_toggle( $attr, $context ) {
	$attr = carelib_attr_button( $attr, $context );

	if ( ! empty( $context ) ) {
		$attr['id'] = "menu-toggle-{$context}";
	}

	return $attr;
}

/**
 * Nav menu attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_menu( $attr, $context ) {
	$attr['role']  = 'navigation';

	if ( ! empty( $context ) ) {
		$attr['id'] = "menu-{$context}";

		if ( ! $menu_name = carelib_get_menu_location_name( $context ) ) {
			// Translators: The %s is the menu name. This is used for the 'aria-label' attribute.
			$attr['aria-label'] = esc_attr( sprintf( _x( '%s Menu', 'nav menu aria label', 'carelib' ), $menu_name ) );
		}
	}

	$attr['itemscope']  = 'itemscope';
	$attr['itemtype']   = 'http://schema.org/SiteNavigationElement';

	return $attr;
}

/**
 * Attributes for nav elements which aren't necessarily site navigation menus.
 * One example use case for this would be pagination and page link blocks.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_nav( $attr, $context ) {
	$attr['role']  = 'navigation';

	if ( ! empty( $context ) ) {
		$attr['id'] = "nav-{$context}";
	}

	return $attr;
}

/**
 * <head> attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_head( $attr ) {
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebSite';

	return $attr;
}

/**
 * Branding (usually a wrapper for title and tagline) attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_branding( $attr ) {
	$attr['id'] = 'site-branding';

	return $attr;
}

/**
 * Site title attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_title( $attr ) {
	$attr['id']       = 'site-title';
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Site description attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_site_description( $attr ) {
	$attr['id']       = 'site-description';
	$attr['itemprop'] = 'description';

	return $attr;
}

/**
 * Archive header attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_archive_header( $attr ) {
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/WebPageElement';

	return $attr;
}

/**
 * Archive title attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_archive_title( $attr ) {
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Archive description attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_archive_description( $attr ) {
	$attr['itemprop'] = 'text';

	return $attr;
}

/**
 * Post <article> element attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_post( $attr ) {
	$attr['id']    = 'post-0';
	$attr['class'] = 'entry';

	$post = get_post();

	if ( $post instanceof WP_Post ) {
		$attr['id']        = 'post-' . get_the_ID();
		$attr['class']     = join( ' ', get_post_class() );
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/CreativeWork';
	}

	return $attr;
}

/**
 * Post title attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_entry_title( $attr ) {
	$attr['itemprop'] = 'headline';

	return $attr;
}

/**
 * Post author attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_entry_author( $attr ) {
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Post time/published attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_entry_published( $attr ) {
	$attr['class']    = 'entry-published updated';
	$attr['datetime'] = get_the_time( 'Y-m-d\TH:i:sP' );
	$attr['itemprop'] = 'datePublished';

	// Translators: Post date/time "title" attribute.
	$attr['title'] = get_the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'carelib' ) );

	return $attr;
}

/**
 * Post content (not excerpt) attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_entry_content( $attr ) {
	$attr['itemprop'] = 'text';

	return $attr;
}

/**
 * Post summary/excerpt attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_entry_summary( $attr ) {
	$attr['class']    = 'entry-content summary';
	$attr['itemprop'] = 'description';

	return $attr;
}

/**
 * Post terms (tags, categories, etc.) attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array
 */
function carelib_attr_entry_terms( $attr, $context ) {
	if ( ! empty( $context ) ) {
		if ( 'post_tag' === $context ) {
			$attr['itemprop'] = 'keywords';
		}
	}

	return $attr;
}

/**
 * Comment wrapper attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_comment( $attr ) {
	$attr['id']    = 'comment-' . get_comment_ID();
	$attr['class'] = join( ' ', get_comment_class() );

	if ( in_array( get_comment_type(), array( '', 'comment' ), true ) ) {
		$attr['itemprop']  = 'comment';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Comment';
	}

	return $attr;
}

/**
 * Comment author attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_comment_author( $attr ) {
	$attr['itemprop']  = 'author';
	$attr['itemscope'] = 'itemscope';
	$attr['itemtype']  = 'http://schema.org/Person';

	return $attr;
}

/**
 * Comment time/published attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_comment_published( $attr ) {
	$attr['datetime'] = get_comment_time( 'Y-m-d\TH:i:sP' );

	// Translators: Comment date/time "title" attribute.
	$attr['title']    = get_comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'carelib' ) );
	$attr['itemprop'] = 'datePublished';

	return $attr;
}

/**
 * Comment permalink attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_comment_permalink( $attr ) {
	$attr['href']     = get_comment_link();
	$attr['itemprop'] = 'url';

	return $attr;
}

/**
 * Comment content/text attributes.
 *
 * @since  1.0.0
 * @access public
 * @param  array $attr Existing attributes.
 * @return array
 */
function carelib_attr_comment_content( $attr ) {
	$attr['itemprop'] = 'text';

	return $attr;
}

/**
 * Filter attributes for the footer widgets widget area.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr Existing attributes.
 * @param  string $context A specific context (e.g., 'primary').
 * @return array $attr Amended attributes.
 */
function carelib_attr_footer_widgets( $attr, $context ) {
	if ( empty( $context ) ) {
		$attr['id'] = 'footer-widgets';
	}

	return $attr;
}
