<?php
/**
 * The main image function for displaying an image. This is a wrapper for the Get_The_Image class. Use this
 * function in themes rather than the class.
 *
 * @since  0.1.0
 * @access public
 * @param  array        $args  Arguments for how to load and display the image.
 * @return string|array        The HTML for the image. | Image attributes in an array.
 */
function get_the_image( $args = array() ) {
	$image = new Get_The_Image( $args );
	return $image->get_image();
}

/**
 * Shows a breadcrumb for all types of pages. This is a wrapper function for the Breadcrumb_Trail class,
 * which should be used in theme templates.
 *
 * @since  0.1.0
 * @access public
 * @param  array $args Arguments to pass to Breadcrumb_Trail.
 * @return void
 */
function breadcrumb_trail( $args = array() ) {
	$breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args );

	if ( ! is_object( $breadcrumb ) ) {
		$breadcrumb = new Breadcrumb_Trail( $args );
	}

	return $breadcrumb->trail();
}
