<?php
/**
 * SiteCare Footer Widgets Class.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * A class to register and load templates for footer widget areas.
 *
 * @package SiteCareLibrary
 */
class SiteCare_Footer_Widgets {

	private $counter = 1;

	private $footer_widgets;

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   SiteCare_Footer_Widgets::$wp_hooks
	 * @return void
	 */
	public function run() {
		$this->footer_widgets = get_theme_support( 'sitecare-footer-widgets' );
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   SiteCare_Footer_Widgets::register_footer_widgets()
	 * @uses   SiteCare_Footer_Widgets::the_footer_widgets()
	 * @uses   add_action
	 * @return void
	 */
	private function wp_hooks() {
		add_action( 'widgets_init',               array( $this, 'register_footer_widgets' ) );
		add_action( 'hybrid_attr_footer-widgets', array( $this, 'attr_footer_widgets' ) );
		add_action( 'tha_footer_before',          array( $this, 'the_footer_widgets' ) );
	}

	/**
	 * Register footer widget areas based on the number of widget areas the user
	 * wishes to create with `add_theme_support()`.
	 *
	 * @since  0.1.0
	 * @uses   register_sidebar() Register footer widget areas.
	 * @return null Return early if there's no theme support.
	 */
	public function register_footer_widgets() {
		// Return early if we don't have any footer widgets to display.
		if ( ! isset( $this->footer_widgets[0] ) || ! is_numeric( $this->footer_widgets[0] ) ) {
			return;
		}

		$counter = $this->counter;

		while ( $counter <= absint( $this->footer_widgets[0] ) ) {
			hybrid_register_sidebar(
				array(
					'id'          => sprintf( 'footer-%d', $counter ),
					'name'        => sprintf( __( 'Footer %d', 'sitecare-library' ), $counter ),
					'description' => sprintf( __( 'Footer %d widget area.', 'sitecare-library' ), $counter ),
				)
			);

			$counter++;
		}
	}

	/**
	 * Footer widgets element attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array   $attr
	 * @return array
	 */
	function attr_footer_widgets( $attr ) {
		$attr['id']    = 'footer-widgets';
		$attr['class'] = 'footer-widgets';
		return $attr;
	}

	/**
	 * Displays all registered footer widget areas using a template.
	 *
	 * @since  0.1.0
	 * @uses   locate_template() Load the footer widget template.
	 * @return null Return early if there's no theme support.
	 */
	public function the_footer_widgets() {
		// Return early if we don't have any footer widgets to display.
		if ( ! isset( $this->footer_widgets[0] ) || ! is_numeric( $this->footer_widgets[0] ) ) {
			return;
		}

		// Return early if the first widget area has no widgets.
		if ( ! is_active_sidebar( 'footer-1' ) ) {
			return;
		}

		$counter = $this->counter;

		// Use the theme's footer widgets template if it exists.
		if ( '' !== locate_template( 'sitecare/footer-widgets.php' ) ) {
			return require_once locate_template( 'sitecare/footer-widgets.php' );
		}
		require_once sitecare_library()->dir . 'templates/footer-widgets.php';
	}
}
