<?php
/**
 * Adds theme and post type support for features included in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Sets up default theme support.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_theme_support() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'caption',
		'comment-form',
		'comment-list',
		'gallery',
		'search-form',
	) );
}

/**
 * Adds extra support for features not default to the core post types.
 *
 * @since 0.8.0
 * @access public
 * @return void
 */
function carelib_post_type_support() {
	// Add support for excerpts to the 'page' post type.
	add_post_type_support( 'page', array( 'excerpt' ) );

	// Add thumbnail support for audio and video attachments.
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	// Add theme layouts support to core and custom post types.
	add_post_type_support( 'post',              'theme-layouts' );
	add_post_type_support( 'page',              'theme-layouts' );
	add_post_type_support( 'attachment',        'theme-layouts' );

	add_post_type_support( 'forum',             'theme-layouts' );
	add_post_type_support( 'literature',        'theme-layouts' );
	add_post_type_support( 'portfolio_item',    'theme-layouts' );
	add_post_type_support( 'portfolio_project', 'theme-layouts' );
	add_post_type_support( 'product',           'theme-layouts' );
	add_post_type_support( 'restaurant_item',   'theme-layouts' );
}