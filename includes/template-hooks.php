<?php
/**
 * Wrapper functions for custom template hook locations.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes\Template\Hooks
 * @author     WP Site Care
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add a custom action for the archive header.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_archive_header() {
	if ( carelib_has_archive_header() ) {
		do_action( "{$GLOBALS['carelib_prefix']}_archive_header" );
	}
}

/**
 * Add a custom hook for the entry header.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_entry_header() {
	if ( carelib_has_entry_header() ) {
		do_action( "{$GLOBALS['carelib_prefix']}_entry_header" );
	}
}

/**
 * Add a custom hook for the entry meta.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_entry_header_meta() {
	if ( carelib_has_entry_header_meta() ) {
		do_action( "{$GLOBALS['carelib_prefix']}_entry_header_meta" );
	}
}

/**
 * Add a custom hook for the entry footer if the current view has an entry footer.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_entry_footer() {
	if ( carelib_has_entry_footer() ) {
		do_action( "{$GLOBALS['carelib_prefix']}_entry_footer" );
	}
}

/**
 * Add a custom hook for the entry footer if the current view has an entry footer.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_entry_footer_meta() {
	if ( carelib_has_entry_footer_meta() ) {
		do_action( "{$GLOBALS['carelib_prefix']}_entry_footer_meta" );
	}
}
