<?php
/**
 * Temporarily adding some placeholder hook locations until they're added to
 * Theme Hook Alliance. Hopefully these won't be in here very long.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
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
