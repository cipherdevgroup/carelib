<?php
/**
 * CareLib Footer Widgets Class.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * A class to register and load templates for footer widget areas.
 *
 * @package CareLib
 */
class CareLib_Footer_Widgets {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * The CareLib layouts class.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Layouts
	 */
	protected $sidebar;

	/**
	 * The number of footer widget areas to display.
	 *
	 * @since 0.2.0
	 * @var   integer
	 */
	protected $widgets;

	/**
	 * Counter to use when iterating through footer widget areas.
	 *
	 * @since 0.2.0
	 * @var   integer
	 */
	protected $counter = 1;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->prefix  = carelib()->get_prefix();
		$this->sidebar = carelib_class( 'sidebar' );
		$this->widgets = apply_filters( "{$this->prefix}_footer_widgets", 3 );
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Footer_Widgets::$wp_hooks
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
	 * @uses   CareLib_Footer_Widgets::register_footer_widgets()
	 * @uses   CareLib_Footer_Widgets::the_footer_widgets()
	 * @uses   add_action
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'widgets_init',                        array( $this, 'register' ) );
		add_action( "{$this->prefix}_attr_footer-widgets", array( $this, 'attributes' ) );
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
		if ( ! isset( $this->widgets ) || ! is_numeric( $this->widgets ) ) {
			return;
		}

		$counter = $this->counter;

		while ( $counter <= absint( $this->widgets ) ) {
			$this->sidebar->register(
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
		if ( ! isset( $this->widgets[0] ) || ! is_numeric( $this->widgets[0] ) ) {
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
