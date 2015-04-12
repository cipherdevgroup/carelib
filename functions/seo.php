<?php
/**
 * Tweaks and Adjustments for the Hybrid Core Framework.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

// Remove unwanted default Hybrid head elements.
remove_action( 'wp_head',  'hybrid_meta_template', 1 );

add_filter( 'hybrid_site_title', 'sitecare_seo_site_title' );
/**
 * Returns the linked site title wrapped in a `<p>` tag unless on the home page
 * or the main blog page where no other H1 exists.
 *
 * @since  0.1.0
 * @access public
 * @param  string $title
 * @return string
 */
function sitecare_seo_site_title( $title ) {
	if ( is_front_page() || is_home() ) {
		return $title;
	}
	return str_replace( array( '<h1', '</h1' ), array( '<p', '</p' ), $title );
}

add_filter( 'hybrid_site_description', 'sitecare_seo_site_description' );
/**
 * Returns the site description wrapped in a `<p>` tag.
 *
 * @since  0.1.0
 * @access public
 * @param  string $desc
 * @return string
 */
function sitecare_seo_site_description( $desc ) {
	return str_replace( array( '<h2', '</h2' ), array( '<p', '</p' ), $desc );
}

add_filter( 'excerpt_more', 'sitecare_seo_excerpt_more' );
/**
 * Filter the default Hybrid more link to add a rel="nofollow" attribute.
 *
 * @since  0.1.0
 * @access public
 * @param  string $text
 * @return string
 */
function sitecare_seo_excerpt_more( $text ) {
	return str_replace( 'class="more-link"', 'rel="nofollow" class="more-link"', $text );
}
