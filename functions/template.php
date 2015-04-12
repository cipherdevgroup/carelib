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
