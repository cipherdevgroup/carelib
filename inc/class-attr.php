<?php
/**
 * Small adjustments to the default Hybrid Core attributes. Changes include
 * the addition of some markup elements, a change to the entry summary class,
 * and a secondary nav element for non-site nav menus.
 *
 * @package    CareLib
 * @subpackage HybridCore
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Attributes class.
 */
class CareLib_Attributes {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	private function wp_hooks() {
		add_filter( 'hybrid_attr_site-container', array( $this, 'site_container' ) );
		add_filter( 'hybrid_attr_site-inner',     array( $this, 'site_inner' ) );
		add_filter( 'hybrid_attr_wrap',           array( $this, 'wrap' ), 10, 2 );
		add_filter( 'hybrid_attr_entry-summary',  array( $this, 'entry_summary_class' ) );
		add_filter( 'hybrid_attr_nav',            array( $this, 'nav' ), 10, 2 );
	}

	/**
	 * Page site container element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function site_container( $attr ) {
		$attr['id']    = 'site-container';
		$attr['class'] = 'site-container';
		return $attr;
	}

	/**
	 * Page site inner element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function site_inner( $attr ) {
		$attr['id']    = 'site-inner';
		$attr['class'] = 'site-inner';
		return $attr;
	}

	/**
	 * Page wrap element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function wrap( $attr, $context ) {
		if ( empty( $context ) ) {
			return $attr;
		}
		$attr['class'] = "wrap {$context}-wrap";
		return $attr;
	}

	/**
	 * Post summary/excerpt attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @return array
	 */
	public function entry_summary_class( $attr ) {
		$attr['class'] = 'entry-content summary';
		return $attr;
	}

	/**
	 * Attributes for nav elements which aren't necessarily site navigation menus.
	 * One example use case for this would be pagination and page link blocks.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array   $attr
	 * @param  string  $context
	 * @return array
	 */
	public function nav( $attr, $context ) {
		$class = 'nav';

		if ( ! empty( $context ) ) {
			$attr['id'] = "nav-{$context}";
			$class    .= " nav-{$context}";
		}

		$attr['class'] = $class;
		$attr['role']  = 'navigation';

		return $attr;
	}
}
