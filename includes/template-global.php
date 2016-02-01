<?php
/**
 * Template helper functions used for global site elements.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

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
 * @since  1.0.0
 * @param  string $name The name of the specialized template.
 * @return void
 */
function carelib_framework( $name = '' ) {
	$templates = array();
	/**
	 * Fires before the default framework template file is loaded.
	 *
	 * @since 1.0.0
	 * @param string $name The name of the specialized framework template.
	 */
	do_action( "{$GLOBALS['carelib_prefix']}_framework", $name, $templates );

	$name      = (string) $name;
	$templates = (array) $templates;

	if ( ! empty( $name ) ) {
		$templates[] = "templates/framework-{$name}.php";
	}
	$templates[] = 'templates/framework.php';

	locate_template( $templates, true );
}

/**
 * Returns the linked site title wrapped in an `<h1>` tag.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_site_title() {
	if ( $title = get_bloginfo( 'name' ) ) {
		$title = sprintf( '<%1$s %2$s><a href="%3$s" rel="home">%4$s</a></%1$s>',
			is_front_page() || is_home() ? 'h1' : 'p',
			carelib_get_attr( 'site-title' ),
			esc_url( home_url() ),
			$title
		);
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_site_title", $title );
}

/**
 * Return the site description wrapped in a `<p>` tag.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_site_description() {
	if ( $desc = get_bloginfo( 'description' ) ) {
		$desc = sprintf( '<p %s>%s</p>',
			carelib_get_attr( 'site-description' ),
			$desc
		);
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_site_description", $desc );
}

/**
 * Adds microdata to avatars.
 *
 * @since  1.0.0
 * @access public
 * @param  string $avatar
 * @return string
 */
function carelib_get_avatar( $avatar ) {
	return preg_replace( '/(<img.*?)(\/>)/i', '$1itemprop="image" $2', $avatar );
}

/**
 * Format a link to the customizer panel.
 *
 * Since WordPress 4.1, the customizer panel allows for deeplinking, but setting
 * up a link can be rather tedious. This function wraps the query args required
 * to deep link to a customzer panel or control, plus return to the correct page
 * when the customizer is exited by the user.
 *
 * @since  1.0.0
 * @access public
 * @param  array $args options for how the link will be formatted.
 * @return string an escaped link to the WordPress customizer panel.
 */
function carelib_get_customizer_link( $args = array() ) {
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
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_credit_link() {
	$link = sprintf( '<a class="author-link" href="%s" title="%s">%s</a>',
		'http://www.wpsitecare.com',
		__( 'Free WordPress Theme by', 'carelib' ) . ' WP Site Care',
		'WP Site Care'
	);
	return apply_filters( "{$GLOBALS['carelib_prefix']}_credit_link", $link );
}

/**
 * Returns formatted theme information.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_info() {
	$info = '<div class="credit">';
	$info .= sprintf(
		// Translators: 1 is current year, 2 is site name/link, 3 is the theme author name/link.
		__( 'Copyright &#169; %1$s %2$s. Free WordPress Theme by %3$s', 'alpha' ),
		date_i18n( 'Y' ),
		carelib_get_site_link(),
		carelib_get_credit_link()
	);
	$info .= '</div>';
	return apply_filters( "{$GLOBALS['carelib_prefix']}_theme_info", $info );
}

/**
 * Returns a link back to the site.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_site_link() {
	return sprintf( '<a class="site-link" href="%s" rel="home">%s</a>',
		esc_url( home_url() ),
		get_bloginfo( 'name' )
	);
}

/**
 * Returns a link to WordPress.org.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_wp_link() {
	return sprintf( '<a class="wp-link" href="%s">%s</a>',
		esc_url( __( 'http://wordpress.org', 'carelib' ) ),
		esc_html__( 'WordPress', 'carelib' )
	);
}

/**
 * Returns a link to the parent theme URI.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_theme_link() {
	$theme = carelib_get_parent();
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
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_child_theme_link() {
	if ( ! is_child_theme() ) {
		return '';
	}

	$theme   = carelib_get_theme();
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
