<?php
/**
 * Template Adjustments for the Hybrid Core Framework.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

add_filter( 'hybrid_content_template_hierarchy', 'sitecare_content_template_hierarchy' );
/**
 * Search the template paths and replace them with singular and archive versions.
 *
 * By default, the content template hierarchy forces you to add logic for single
 * and archive content within the templates themselves. This makes the templates
 * overly complex and I would prefer to separate them into individual files.
 *
 * @since  0.1.0
 * @access public
 * @param  array $templates
 * @return array $templates
 */
function sitecare_content_template_hierarchy( $templates ) {
	if ( is_singular() || is_attachment() ) {
		$templates = str_replace( 'content/', 'content/singular/', $templates );
	} else {
		$templates = str_replace( 'content/', 'content/archive/', $templates );
	}
	return $templates;
}

/**
 * Temporarily adding some placeholder hook locations until they're added to
 * Theme Hook Alliance. Hopefully these won't be in here very long.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
if ( ! function_exists( 'tha_content_while_before' ) ) {
	function tha_content_while_before() {
		do_action( 'tha_content_while_before' );
	}
}

if ( ! function_exists( 'tha_content_while_after' ) ) {
	function tha_content_while_after() {
		do_action( 'tha_content_while_after' );
	}
}

if ( ! function_exists( 'tha_entry_content_before' ) ) {
	function tha_entry_content_before() {
		do_action( 'tha_entry_content_before' );
	}
}

if ( ! function_exists( 'tha_entry_content_after' ) ) {
	function tha_entry_content_after() {
		do_action( 'tha_entry_content_after' );
	}
}
