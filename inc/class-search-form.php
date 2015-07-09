<?php
/**
 * The default WordPress search form is lacking in terms of accessibility.
 * In order to bring it into compliance with WCAG we need to make a few changes.
 * This class adds some aria labels and a unique ID to each search form instance.
 * It also applies some filters which can be used to control the output of the
 * search form.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Search Form Class.
 */
class CareLib_Search_Form {

	protected $id;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

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
	protected function wp_hooks() {
		add_filter( 'get_search_form', array( $this, 'get_form' ), 99 );
	}

	/**
	 * Get the search form elements and return them as a single string.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_form() {
		return sprintf(
			'<form class="search-form" method="get" action="%s" role="search">%s</form>',
			esc_url( home_url( '/' ) ),
			$this->get_label() . $this->get_input() . $this->get_button()
		);
	}

	/**
	 * Get the search form label.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function get_label() {
		$label = apply_filters( "{$this->prefix}_search_form_label", __( 'Search site', 'carelib' ) );

		return sprintf(
			'<label id="%1$s-label" for="%1$s" class="screen-reader-text">%2$s</label>',
			esc_attr( $this->get_id() ),
			esc_attr( $label )
		);
	}

	/**
	 * Get the search form input field.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function get_input() {
		$value = get_search_query() ? apply_filters( 'the_search_query', get_search_query() ) : '';
		$placeholder = apply_filters( "{$this->prefix}_search_text", __( 'Search this website', 'carelib' ) );

		return sprintf(
			'<input type="search" name="s" id="%s" placeholder="%s" autocomplete="off" value="%s" />',
			esc_attr( $this->get_id() ),
			esc_attr( $placeholder ),
			esc_attr( $value )
		);
	}

	/**
	 * Get the search form button element.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function get_button() {
		return sprintf(
			'<button type="submit" aria-label="%1$s"><span class="screen-reader-text">%2$s</span></button>',
			esc_attr( apply_filters( "{$this->prefix}_search_button_label", __( 'Search', 'carelib' ) ) ),
			esc_attr( apply_filters( "{$this->prefix}_search_button_text", __( 'Search', 'carelib' ) ) )
		);
	}

	/**
	 * Generate a unique ID for each search form.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function get_id() {
		if ( ! isset( $this->id ) ) {
			$this->id = uniqid( 'searchform-' );
		}
		return $this->id;
	}
}
