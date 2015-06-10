<?php
/**
 * CareLib Footer Widgets Class.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * A class to register and load templates for footer widget areas.
 *
 * @package CareLib
 */
class CareLib_Footer_Widgets {

	private $counter = 1;

	private $footer_widgets;

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Footer_Widgets::$wp_hooks
	 * @return void
	 */
	public function run() {
		$this->footer_widgets = get_theme_support( 'carelib-footer-widgets' );
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Footer_Widgets::register_footer_widgets()
	 * @uses   CareLib_Footer_Widgets::the_footer_widgets()
	 * @uses   add_action
	 * @return void
	 */
	private function wp_hooks() {
		add_action( 'widgets_init',               array( $this, 'register' ) );
		add_action( 'hybrid_attr_footer-widgets', array( $this, 'attributes' ) );
		add_action( 'tha_footer_before',          array( $this, 'template' ) );
	}

	/**
	 * Register footer widget areas based on the number of widget areas the user
	 * wishes to create with `add_theme_support()`.
	 *
	 * @since  0.1.0
	 * @uses   register_sidebar() Register footer widget areas.
	 * @return null Return early if there's no theme support.
	 */
	public function register() {
		// Return early if we don't have any footer widgets to display.
		if ( ! isset( $this->footer_widgets[0] ) || ! is_numeric( $this->footer_widgets[0] ) ) {
			return;
		}

		$counter = $this->counter;

		while ( $counter <= absint( $this->footer_widgets[0] ) ) {
			hybrid_register_sidebar(
				array(
					'id'          => sprintf( 'footer-%d', $counter ),
					'name'        => sprintf( __( 'Footer %d', 'carelib' ), $counter ),
					'description' => sprintf( __( 'Footer %d widget area.', 'carelib' ), $counter ),
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
	function attributes( $attr ) {
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
	public function template() {
		// Return early if we don't have any footer widgets to display.
		if ( ! isset( $this->footer_widgets[0] ) || ! is_numeric( $this->footer_widgets[0] ) ) {
			return false;
		}

		// Return early if the first widget area has no widgets.
		if ( ! is_active_sidebar( 'footer-1' ) ) {
			return false;
		}

		$counter  = $this->counter;
		$template = locate_template( 'hooked/footer-widgets.php' );

		// Use the theme's archive author box template if it exists.
		if ( ! empty( $template ) ) {
			require_once $template;
		}
	}
}
