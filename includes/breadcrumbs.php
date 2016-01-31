<?php
/**
 * Methods for controlling breadcrumb display.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * An array of breadcrumb locations.
 *
 * @since  0.1.0
 * @access public
 * @return array $breadcrumbs
 */
function carelib_get_breadcrumb_options() {
	$prefix = $GLOBALS['carelib_prefix'];

	return apply_filters( "{$prefix}_breadcrumb_options", array(
		"{$prefix}_breadcrumb_single" => array(
			'default'  => 0,
			'label'    => __( 'Single Entries', 'carelib' ),
		),
		"{$prefix}_breadcrumb_pages" => array(
			'default'  => 0,
			'label'    => __( 'Pages', 'carelib' ),
		),
		"{$prefix}_breadcrumb_blog_page" => array(
			'default'  => 0,
			'label'    => __( 'Blog Page', 'carelib' ),
		),
		"{$prefix}_breadcrumb_archive" => array(
			'default'  => 0,
			'label'    => __( 'Archives', 'carelib' ),
		),
		"{$prefix}_breadcrumb_404" => array(
			'default'  => 0,
			'label'    => __( '404 Page', 'carelib' ),
		),
		"{$prefix}_breadcrumb_attachment" => array(
			'default'  => 0,
			'label'    => __( 'Attachment/Media Pages', 'carelib' ),
		),
	) );
}

/**
 * Display our breadcrumbs based on selections made in the WordPress customizer.
 *
 * @since  0.1.0
 * @access public
 * @return bool true if both our template tag and theme mod return true.
 */
function carelib_display_breadcrumbs() {
	// Grab our available breadcrumb display options.
	$options = array_keys( carelib_get_breadcrumb_options() );
	// Set up an array of template tags to map to our breadcrumb display options.
	$tags = apply_filters( "{$GLOBALS['carelib_prefix']}_breadcrumb_tags",
		array(
			is_singular() && ! is_attachment() && ! is_page(),
			is_page(),
			is_home() && ! is_front_page(),
			is_archive(),
			is_404(),
			is_attachment(),
		)
	);

	// Loop through our theme mods to see if we have a match.
	foreach ( array_combine( $options, $tags ) as $mod => $tag ) {
		// Return true if we find an enabled theme mod within the correct section.
		if ( 1 === absint( get_theme_mod( $mod, 0 ) ) && true === $tag ) {
			return true;
		}
	}
	return false;
}

/**
 * Check to see if a supported breadcrumbs plugin is active.
 *
 * @since  0.1.0
 * @access public
 * @return mixed false if no plugin is active, callback function name if one is
 */
function carelib_breadcrumb_plugin_is_active() {
	$callbacks = apply_filters( "{$GLOBALS['carelib_prefix']}_breadcrumbs_plugins", array(
		'yoast_breadcrumb',
		'breadcrumb_trail',
		'bcn_display',
		'breadcrumbs',
		'crumbs',
	) );

	foreach ( (array) $callbacks as $callback ) {
		if ( function_exists( $callback ) ) {
			return $callback;
		}
	}
	return false;
}
