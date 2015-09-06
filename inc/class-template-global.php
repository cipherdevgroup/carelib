<?php
/**
 * Template helper functions used for global site elements.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Template Tags Class.
 */
class CareLib_Template_Global {

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
	 * Load the base theme framework template.
	 *
	 * This works similarly to WP core's `get_template_part` except it imposes
	 * some restrictions on the template's name and location.
	 *
	 * It also uses `require_once` as there should only be a single framework
	 * template loaded on any given page.
	 *
	 * For the $name parameter, if the file is called `framework-special.php`
	 * then specify "special".
	 *
	 * @since  0.2.0
	 * @param  string $name The name of the specialized template.
	 * @return void
	 */
	public function framework( $name = null ) {
		/**
		 * Fires before the default framework template file is loaded.
		 *
		 * @since 0.2.0
		 * @param string $name The name of the specialized framework template.
		 */
		do_action( "{$this->prefix}_framework", $name );
		$templates = array();
		$name = (string) $name;
		if ( ! empty( $name ) ) {
			$templates[] = "templates/framework-{$name}.php";
		}
		$templates[] = 'templates/framework.php';
		locate_template( $templates, true );
	}

	/**
	 * Returns the linked site title wrapped in an `<h1>` tag.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_title() {
		if ( $title = get_bloginfo( 'name' ) ) {
			$title = sprintf( '<%1$s %2$s><a href="%3$s" rel="home">%4$s</a></%1$s>',
				is_front_page() || is_home() ? 'h1' : 'p',
				carelib_get( 'attributes' )->get_attr( 'site-title' ),
				esc_url( home_url() ),
				$title
			);
		}

		return apply_filters( "{$this->prefix}_site_title", $title );
	}

	/**
	 * Return the site description wrapped in a `<p>` tag.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_description() {
		if ( $desc = get_bloginfo( 'description' ) ) {
			$desc = sprintf( '<p %s>%s</p>',
				carelib_get( 'attributes' )->get_attr( 'site-description' ),
				$desc
			);
		}

		return apply_filters( "{$this->prefix}_site_description", $desc );
	}

	/**
	 * Retrieve the site logo URL or ID (URL by default). Pass in the string
	 * 'id' for ID.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::get_site_logo
	 * @param  string $format the format to return
	 * @return mixed The URL or ID of our site logo, false if not set
	 */
	public function get_logo( $format = 'url' ) {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_get_site_logo( $format );
		}
		return carelib_get( 'site-logo' )->get_site_logo( $format );
	}

	/**
	 * Determine if a site logo is assigned or not.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::has_site_logo
	 * @return boolean True if there is an active logo, false otherwise
	 */
	public function has_logo() {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			return jetpack_has_site_logo();
		}
		return carelib_get( 'site-logo' )->has_site_logo();
	}

	/**
	 * Output an <img> tag of the site logo, at the size specified
	 * in the theme's add_theme_support() declaration.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Site_Logo::the_site_logo
	 * @return void
	 */
	public function the_logo() {
		if ( function_exists( 'jetpack_the_site_logo' ) ) {
			jetpack_the_site_logo();
			return;
		}
		carelib_get( 'site-logo' )->the_site_logo();
	}

	/**
	 * Display our breadcrumbs based on selections made in the WordPress customizer.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool true if both our template tag and theme mod return true.
	 */
	public function display_breadcrumbs() {
		$breadcrumbs = carelib_get( 'breadcrumbs' );
		// Return early if our theme doesn't support breadcrumbs.
		if ( ! is_object( $breadcrumbs ) ) {
			return false;
		}
		return $breadcrumbs->display();
	}

	/**
	 * Format a link to the customizer panel.
	 *
	 * Since WordPress 4.1, the customizer panel allows for deeplinking, but setting
	 * up a link can be rather tedious. This function wraps the query args required
	 * to deep link to a customzer panel or control, plus return to the correct page
	 * when the customizer is exited by the user.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array options for how the link will be formatted
	 * @return string an escaped link to the WordPress customizer panel.
	 */
	public function get_customizer_link( $args = array() ) {
		$defaults = array(
			'focus_type'   => '',
			'focus_target' => '',
			'return'       => get_permalink(),
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array();
		$type       = $args['focus_type'];
		$target     = $args['focus_target'];
		$return     = $args['return'];

		if ( ! empty( $type ) && ! empty( $target ) ) {
			$query_args[] = array( 'autofocus' => array( $type => $target ) );
		}
		if ( ! empty( $return ) ) {
			$query_args['return'] = urlencode( wp_unslash( $return ) );
		}

		return esc_url( add_query_arg( $query_args, admin_url( 'customize.php' ) ) );
	}

	/**
	 * Returns a formatted theme credit link.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_credit_link() {
		$link = sprintf( '<a class="author-link" href="%s" title="%s">%s</a>',
			'http://www.wpsitecare.com',
			__( 'Free WordPress Theme by', 'carelib' ) . ' WP Site Care',
			'WP Site Care'
		);
		return apply_filters( "{$this->prefix}_credit_link", $link );
	}

	/**
	 * Returns formatted theme information.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_theme_info() {
		$info = '<div class="credit">';
		$info .= sprintf(
			// Translators: 1 is current year, 2 is site name/link, 3 is the theme author name/link.
			__( 'Copyright &#169; %1$s %2$s. Free WordPress Theme by %3$s', 'alpha' ),
			date_i18n( 'Y' ),
			carelib_get_site_link(),
			$this->get_credit_link()
		);
		$info .= '</div>';
		return apply_filters( "{$this->prefix}_theme_info", $info );
	}

	/**
	 * Returns a link back to the site.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_site_link() {
		return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>',
			esc_url( home_url() ),
			get_bloginfo( 'name' )
		);
	}

	/**
	 * Returns a link to WordPress.org.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_wp_link() {
		return sprintf( '<a class="wp-link" href="%s">%s</a>',
			esc_url( __( 'http://wordpress.org', 'carelib' ) ),
			esc_html__( 'WordPress', 'carelib' )
		);
	}

	/**
	 * Returns a link to the parent theme URI.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_theme_link() {
		$theme   = wp_get_theme( get_template() );
		$allowed = array(
			'abbr'    => array( 'title' => true ),
			'acronym' => array( 'title' => true ),
			'code'    => true,
			'em'      => true,
			'strong'  => true,
		);

		// Note: URI is escaped via `WP_Theme::markup_header()`.
		return sprintf( '<a class="theme-link" href="%s">%s</a>',
			$theme->display( 'ThemeURI' ),
			wp_kses( $theme->display( 'Name' ), $allowed )
		);
	}

	/**
	 * Returns a link to the child theme URI.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_child_theme_link() {
		if ( ! is_child_theme() ) {
			return '';
		}

		$theme   = wp_get_theme();
		$allowed = array(
			'abbr'    => array( 'title' => true ),
			'acronym' => array( 'title' => true ),
			'code'    => true,
			'em'      => true,
			'strong'  => true,
		);

		// Note: URI is escaped via `WP_Theme::markup_header()`.
		return sprintf( '<a class="child-link" href="%s">%s</a>',
			$theme->display( 'ThemeURI' ),
			wp_kses( $theme->display( 'Name' ),	$allowed )
		);
	}

	/**
	 * Gets the "blog" (posts page) page URL. `home_url()` will not always work for this because it
	 * returns the front page URL. Sometimes the blog page URL is set to a different page. This
	 * function handles both scenarios.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_blog_url() {
		if ( 'posts' === get_option( 'show_on_front' ) ) {
			$blog_url = home_url();
		}

		if ( 0 < ( $page_for_posts = get_option( 'page_for_posts' ) ) ) {
			$blog_url = get_permalink( $page_for_posts );
		}

		return empty( $blog_url ) ? '' : esc_url( $blog_url );
	}

}
