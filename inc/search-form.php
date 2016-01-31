<?php
/**
 * The default WordPress search form is lacking in terms of accessibility.
 * In order to bring it into compliance with WCAG we need to make a few changes.
 * This class adds some aria labels and a unique ID to each search form instance.
 * It also applies some filters which can be used to control the output of the
 * search form.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

/**
 * Get the search form elements and return them as a single string.
 *
 * @since  0.1.0
 * @access public
 * @return string
 */
function carelib_search_form_get_form() {
	return sprintf(
		'<form class="search-form" method="get" action="%s" role="search">%s</form>',
		esc_url( home_url( '/' ) ),
		_carelib_search_form_get_label() . _carelib_search_form_get_input() . _carelib_search_form_get_button()
	);
}

/**
 * Get the search form label.
 *
 * @since  0.1.0
 * @access protected
 * @return string
 */
function _carelib_search_form_get_label() {
	$label = apply_filters( "{$GLOBALS['carelib_prefix']}_search_form_label", __( 'Search site', 'carelib' ) );

	return sprintf(
		'<label id="%1$s-label" for="%1$s" class="screen-reader-text">%2$s</label>',
		esc_attr( _carelib_search_form_get_id() ),
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
function _carelib_search_form_get_input() {
	$value = get_search_query() ? apply_filters( 'the_search_query', get_search_query() ) : '';
	$placeholder = apply_filters( "{$GLOBALS['carelib_prefix']}_search_text", __( 'Search this website', 'carelib' ) );

	return sprintf(
		'<input type="search" name="s" id="%s" placeholder="%s" autocomplete="off" value="%s" />',
		esc_attr( _carelib_search_form_get_id() ),
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
function _carelib_search_form_get_button() {
	return sprintf(
		'<button type="submit" aria-label="%1$s">%2$s</button>',
		esc_attr( apply_filters( "{$GLOBALS['carelib_prefix']}_search_button_label", __( 'Search', 'carelib' ) ) ),
		apply_filters( "{$GLOBALS['carelib_prefix']}_search_button_text", sprintf( '<span class="screen-reader-text">%s</span>',
			esc_html__( 'Search', 'carelib' )
		) )
	);
}

/**
 * Generate a unique ID for each search form.
 *
 * @since  0.1.0
 * @access protected
 * @return string
 */
function _carelib_search_form_get_id() {
	$id = false;

	if ( ! $id ) {
		$id = uniqid( 'searchform-' );
	}

	return $id;
}
