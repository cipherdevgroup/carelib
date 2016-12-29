<?php
/**
 * Wrapper functions for custom template hook locations.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes\Template\Hooks
 * @author     WP Site Care
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

/**
 * Add a custom action for the archive header.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_archive_header() {
	if ( carelib_has_archive_header() ) {
		do_action( 'carelib_archive_header' );
	}
}

/**
 * Add a custom hook for the entry header.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_entry_header() {
	if ( carelib_has_entry_header() ) {
		do_action( 'carelib_entry_header' );
	}
}

/**
 * Add a custom hook for the entry meta.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_entry_header_meta() {
	if ( carelib_has_entry_header_meta() ) {
		do_action( 'carelib_entry_header_meta' );
	}
}

/**
 * Add a custom hook for the entry content.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_entry_content() {
	do_action( 'carelib_entry_content' );
}

/**
 * Add a custom hook for the entry footer if the current view has an entry footer.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_entry_footer() {
	if ( carelib_has_entry_footer() ) {
		do_action( 'carelib_entry_footer' );
	}
}

/**
 * Add a custom hook for the entry footer if the current view has an entry footer.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function carelib_entry_footer_meta() {
	if ( carelib_has_entry_footer_meta() ) {
		do_action( 'carelib_entry_footer_meta' );
	}
}
