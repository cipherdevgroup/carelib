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

class CareLib_Template_Hooks {
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
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Add a custom hook for the entry header.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function entry_header() {
		if ( carelib_get( 'template-entry' )->has_entry_header() ) {
			do_action( "{$this->prefix}_entry_header" );
		}
	}

	/**
	 * Add a custom hook for the entry meta.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function entry_header_meta() {
		if ( carelib_get( 'template-entry' )->has_entry_header_meta() ) {
			do_action( "{$this->prefix}_entry_header_meta" );
		}
	}

	/**
	 * Add a custom hook for the entry footer if the current view has an entry footer.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function entry_footer() {
		if ( carelib_get( 'template-entry' )->has_entry_footer() ) {
			do_action( "{$this->prefix}_entry_footer" );
		}
	}

	/**
	 * Add a custom hook for the entry footer if the current view has an entry footer.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function entry_footer_meta() {
		if ( carelib_get( 'template-entry' )->has_entry_footer_meta() ) {
			do_action( "{$this->prefix}_entry_footer_meta" );
		}
	}
}
