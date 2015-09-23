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
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Attributes {

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
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->attr_filters( "{$this->prefix}_attr" );
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	protected function attr_filters( $prefix ) {
		add_filter( "{$prefix}_body",           array( $this, 'body' ),           5 );
		add_filter( "{$prefix}_header",         array( $this, 'header' ),         5 );
		add_filter( "{$prefix}_site-container", array( $this, 'site_container' ), 5 );
		add_filter( "{$prefix}_site-inner",     array( $this, 'site_inner' ),     5 );
		add_filter( "{$prefix}_footer",         array( $this, 'footer' ),         5 );
		add_filter( "{$prefix}_content",        array( $this, 'content' ),        5 );
		add_filter( "{$prefix}_sidebar",        array( $this, 'sidebar' ),        5, 2 );
		add_filter( "{$prefix}_menu",           array( $this, 'menu' ),           5, 2 );
		add_filter( "{$prefix}_nav",            array( $this, 'nav' ),            5, 2 );
		add_filter( "{$prefix}_wrap",           array( $this, 'wrap' ),           5, 2 );

		# Header attributes.
		add_filter( "{$prefix}_head",             array( $this, 'head' ),             5 );
		add_filter( "{$prefix}_branding",         array( $this, 'branding' ),         5 );
		add_filter( "{$prefix}_site-title",       array( $this, 'site_title' ),       5 );
		add_filter( "{$prefix}_site-description", array( $this, 'site_description' ), 5 );

		# Archive page header attributes.
		add_filter( "{$prefix}_archive-header",      array( $this, 'archive_header' ),      5 );
		add_filter( "{$prefix}_archive-title",       array( $this, 'archive_title' ),       5 );
		add_filter( "{$prefix}_archive-description", array( $this, 'archive_description' ), 5 );

		# Post-specific attributes.
		add_filter( "{$prefix}_post",            array( $this, 'post' ),            5 );
		add_filter( "{$prefix}_entry",           array( $this, 'post' ),            5 ); // Alternate for "post".
		add_filter( "{$prefix}_entry-title",     array( $this, 'entry_title' ),     5 );
		add_filter( "{$prefix}_entry-author",    array( $this, 'entry_author' ),    5 );
		add_filter( "{$prefix}_entry-published", array( $this, 'entry_published' ), 5 );
		add_filter( "{$prefix}_entry-content",   array( $this, 'entry_content' ),   5 );
		add_filter( "{$prefix}_entry-summary",   array( $this, 'entry_summary' ),   5 );
		add_filter( "{$prefix}_entry-terms",     array( $this, 'entry_terms' ),     5, 2 );

		# Comment specific attributes.
		add_filter( "{$prefix}_comment",           array( $this, 'comment' ),           5 );
		add_filter( "{$prefix}_comment-author",    array( $this, 'comment_author' ),    5 );
		add_filter( "{$prefix}_comment-published", array( $this, 'comment_published' ), 5 );
		add_filter( "{$prefix}_comment-permalink", array( $this, 'comment_permalink' ), 5 );
		add_filter( "{$prefix}_comment-content",   array( $this, 'comment_content' ),   5 );
	}

	/**
	 * Get an HTML element's attributes.
	 *
	 * This function is actually meant to be filtered by theme authors, plugins,
	 * or advanced child theme users. The purpose is to allow folks to modify,
	 * remove, or add any attributes they want without having to edit every
	 * template file in the theme. So, one could support microformats instead
	 * of microdata, if desired.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $slug     The slug/ID of the element (e.g., 'sidebar').
	 * @param  string  $context  A specific context (e.g., 'primary').
	 * @return string
	 */
	public function get_attr( $slug, $context = '', $attr = array() ) {
		$out  = '';
		$attr = array_merge(
			(array) $attr,
			(array) apply_filters( "{$this->prefix}_attr_{$slug}", $attr, $context )
		);

		if ( empty( $attr ) ) {
			$attr['class'] = $slug;
		}

		foreach ( $attr as $name => $value ) {
			$out .= ! empty( $value ) ? sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) ) : esc_html( " {$name}" );
		}

		return trim( $out );
	}

	/**
	 * Page wrap element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function wrap( $attr, $context ) {
		if ( empty( $context ) ) {
			return $attr;
		}
		$attr['class'] = "wrap {$context}-wrap";
		return $attr;
	}

	/**
	 * <body> element attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function body( $attr ) {
		$attr['class']     = join( ' ', get_body_class() );
		$attr['dir']       = is_rtl() ? 'rtl' : 'ltr';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WebPage';

		if ( is_singular( 'post' ) || is_home() || is_archive() ) {
			$attr['itemtype'] = 'http://schema.org/Blog';
		}
		if ( is_search() ) {
			$attr['itemtype'] = 'http://schema.org/SearchResultsPage';
		}

		return $attr;
	}

	/**
	 * Page <header> element attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function header( $attr ) {
		$attr['id']        = 'header';
		$attr['class']     = 'site-header';
		$attr['role']      = 'banner';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WPHeader';

		return $attr;
	}

	/**
	 * Page site container element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function site_container( $attr ) {
		$attr['id']    = 'site-container';
		$attr['class'] = 'site-container';
		return $attr;
	}

	/**
	 * Page site inner element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function site_inner( $attr ) {
		$attr['id']    = 'site-inner';
		$attr['class'] = 'site-inner';
		return $attr;
	}

	/**
	 * Page <footer> element attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function footer( $attr ) {
		$attr['id']        = 'footer';
		$attr['class']     = 'site-footer';
		$attr['role']      = 'contentinfo';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WPFooter';

		return $attr;
	}

	/**
	 * Main content container of the page attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function content( $attr ) {
		$attr['id']       = 'content';
		$attr['class']    = 'content';
		$attr['role']     = 'main';

		if ( ! is_singular( 'post' ) && ! is_home() && ! is_archive() ) {
			$attr['itemprop'] = 'mainContentOfPage';
		}

		return $attr;
	}

	/**
	 * Sidebar attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function sidebar( $attr, $context ) {
		$attr['class'] = 'sidebar';
		$attr['role']  = 'complementary';

		if ( ! empty( $context ) ) {

			$attr['class'] .= " sidebar-{$context}";
			$attr['id']     = "sidebar-{$context}";

			if ( $name = carelib_get( 'sidebar' )->get_name( $context ) ) {
				// Translators: The %s is the sidebar name. This is used for the 'aria-label' attribute.
				$attr['aria-label'] = esc_attr( sprintf( _x( '%s Sidebar', 'sidebar aria label', 'carelib' ), $name ) );
			}
		}

		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WPSideBar';

		return $attr;
	}

	/**
	 * Function for grabbing a WP nav menu theme location name.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $location
	 * @return string
	 */
	protected function get_menu_location_name( $location ) {
		$locations = get_registered_nav_menus();
		return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
	}

	/**
	 * Nav menu attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function menu( $attr, $context ) {
		$attr['class'] = 'menu';
		$attr['role']  = 'navigation';

		if ( ! empty( $context ) ) {

			$attr['class'] .= " menu-{$context}";
			$attr['id']     = "menu-{$context}";

			$menu_name = $this->get_menu_location_name( $context );

			if ( ! empty( $menu_name ) ) {
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
	 * @since  0.1.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function nav( $attr, $context ) {
		$class = 'nav';

		if ( ! empty( $context ) ) {
			$attr['id'] = "nav-{$context}";
			$class    .= " nav-{$context}";
		}

		$attr['class'] = $class;
		$attr['role']  = 'navigation';

		return $attr;
	}

	/**
	 * <head> attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function head( $attr ) {
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WebSite';

		return $attr;
	}

	/**
	 * Branding (usually a wrapper for title and tagline) attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function branding( $attr ) {
		$attr['id']    = 'branding';
		$attr['class'] = 'site-branding';

		return $attr;
	}

	/**
	 * Site title attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function site_title( $attr ) {
		$attr['id']       = 'site-title';
		$attr['class']    = 'site-title';
		$attr['itemprop'] = 'headline';

		return $attr;
	}

	/**
	 * Site description attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function site_description( $attr ) {
		$attr['id']       = 'site-description';
		$attr['class']    = 'site-description';
		$attr['itemprop'] = 'description';

		return $attr;
	}

	/**
	 * Archive header attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function archive_header( $attr ) {
		$attr['class']     = 'archive-header';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/WebPageElement';

		return $attr;
	}

	/**
	 * Archive title attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function archive_title( $attr ) {
		$attr['class']     = 'archive-title';
		$attr['itemprop']  = 'headline';

		return $attr;
	}

	/**
	 * Archive description attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function archive_description( $attr ) {
		$attr['class']     = 'archive-description';
		$attr['itemprop']  = 'text';

		return $attr;
	}

	/**
	 * Post <article> element attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function post( $attr ) {
		$attr['id']    = 'post-0';
		$attr['class'] = join( ' ', get_post_class() );

		$post = get_post();
		if ( is_object( $post ) ) {
			$attr['id']        = 'post-' . get_the_ID();
			$attr['class']     = join( ' ', get_post_class() );
			$attr['itemscope'] = 'itemscope';
			$attr['itemtype']  = 'http://schema.org/CreativeWork';

			if ( 'post' === get_post_type() ) {
				$attr['itemtype']  = 'http://schema.org/BlogPosting';
				if ( is_main_query() && ! is_search() ) {
					$attr['itemprop'] = 'blogPost';
				}
			}
			if ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {
				$attr['itemtype'] = 'http://schema.org/ImageObject';
			}
		}

		return $attr;
	}

	/**
	 * Post title attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function entry_title( $attr ) {
		$attr['class']    = 'entry-title';
		$attr['itemprop'] = 'headline';

		return $attr;
	}

	/**
	 * Post author attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function entry_author( $attr ) {
		$attr['class']     = 'entry-author';
		$attr['itemprop']  = 'author';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Person';

		return $attr;
	}

	/**
	 * Post time/published attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function entry_published( $attr ) {
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
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function entry_content( $attr ) {
		$attr['class']    = 'entry-content';
		$attr['itemprop'] = 'text';

		if ( 'post' === get_post_type() ) {
			$attr['itemprop'] = 'articleBody';
		}

		return $attr;
	}

	/**
	 * Post summary/excerpt attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function entry_summary( $attr ) {
		$attr['class']    = 'entry-content summary';
		$attr['itemprop'] = 'description';

		return $attr;
	}

	/**
	 * Post terms (tags, categories, etc.) attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function entry_terms( $attr, $context ) {
		if ( ! empty( $context ) ) {
			$attr['class'] = 'entry-terms ' . sanitize_html_class( $context );

			if ( 'category' === $context ) {
				$attr['itemprop'] = 'articleSection';
			}
			if ( 'post_tag' === $context ) {
				$attr['itemprop'] = 'keywords';
			}
		}

		return $attr;
	}

	/**
	 * Comment wrapper attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function comment( $attr ) {
		$attr['id']    = 'comment-' . get_comment_ID();
		$attr['class'] = join( ' ', get_comment_class() );

		if ( in_array( get_comment_type(), array( '', 'comment' ) ) ) {
			$attr['itemprop']  = 'comment';
			$attr['itemscope'] = 'itemscope';
			$attr['itemtype']  = 'http://schema.org/Comment';
		}

		return $attr;
	}

	/**
	 * Comment author attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function comment_author( $attr ) {
		$attr['class']     = 'comment-author';
		$attr['itemprop']  = 'author';
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Person';

		return $attr;
	}

	/**
	 * Comment time/published attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function comment_published( $attr ) {
		$attr['class']    = 'comment-published';
		$attr['datetime'] = get_comment_time( 'Y-m-d\TH:i:sP' );

		// Translators: Comment date/time "title" attribute.
		$attr['title']    = get_comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'carelib' ) );
		$attr['itemprop'] = 'datePublished';

		return $attr;
	}

	/**
	 * Comment permalink attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function comment_permalink( $attr ) {
		$attr['class']    = 'comment-permalink';
		$attr['href']     = get_comment_link();
		$attr['itemprop'] = 'url';

		return $attr;
	}

	/**
	 * Comment content/text attributes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	public function comment_content( $attr ) {
		$attr['class']    = 'comment-content';
		$attr['itemprop'] = 'text';

		return $attr;
	}

}
