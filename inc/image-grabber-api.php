<?php
/**
 * An API for interacting with the CareLib_Image_Grabber class.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

class CareLib_Image_Grabber_API extends CareLib_Image_Grabber {

	/**
	 * Return a grabbed image.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $image an array of image attributes.
	 * @param  array $args Arguments for how to load and display an image.
	 * @return string|array the raw image string or an array of image attributes
	 */
	public function get( $args ) {
		if ( ! $args = $this->setup_args( $args ) ) {
			return false;
		}

		$image = $this->find_the_image( $args );

		if ( is_array( $image ) ) {
			$image = $this->format_image( $args, $image );
		}

		if ( 'array' === $args['format'] ) {
			return $this->get_raw_image( $image );
		}

		return empty( $image ) ? false : "{$args['before']}{$image}{$args['after']}";
	}

}
