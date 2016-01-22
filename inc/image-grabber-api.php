<?php
/**
 * An API for interacting with the CareLib_Image_Grabber class.
 *
 * @package    CareLib
 * @subpackage CareLib/Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

class CareLib_Image_Grabber_API extends CareLib_Image_Grabber {
	/**
	 * Return a grabbed image.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param array $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type int    $post_id The ID of the post associated with the image.
	 *     @type array  $meta_key The meta key of the image in the order to search.
	 *     @type bool   $featured Whether or not to use core's featured image.
	 *     @type bool   $attachment Whether or not to fall back to the first attached image.
	 *     @type string $size The image size to display.
	 *     @type array  $srcset_sizes An array of additional sizes to use within a srcset.
	 *     @type bool|string $default_image URI to a default fallback image for when none is found.
	 *     @type bool   $link_to_post Whether to link to the post associated with the image.
	 *     @type bool   $link_class The class to be applied to the link wrapping the image.
	 *     @type bool   $image_class The class to be applied to the image markup.
	 *     @type bool   $width The width of the image to grab.
	 *     @type bool   $height The height of the image to grab.
	 *     @type bool   $format The format of the image to be returned. Can be img or array.
	 *     @type bool   $meta_key_save Whether or not to save the grabbed image as meta data.
	 *     @type bool   $thumbnail_id_save Whether or not to save the grabbed image as the post's featured image.
	 *     @type bool   $cache Whether or not to cache the grabbed image result.
	 *     @type string $before Markup to output before the grabbed image.
	 *     @type string $after Markup to output after the grabbed image.
	 * }
	 * @return string|array the raw image string or an array of image attributes.
	 */
	public function get( $args = array() ) {
		$args = wp_parse_args( $args, apply_filters( "{$this->prefix}_image_grabber_defaults",
			array(
				'post_id'           => get_the_ID(),
				'meta_key'          => array( 'Thumbnail', 'thumbnail' ),
				'featured'          => true,
				'attachment'        => true,
				'size'              => has_image_size( 'post-thumbnail' ) ? 'post-thumbnail': 'thumbnail',
				'srcset_sizes'      => array(),
				'default_image'     => false,
				'link_to_post'      => true,
				'link_class'        => false,
				'image_class'       => false,
				'width'             => false,
				'height'            => false,
				'format'            => 'img',
				'meta_key_save'     => false,
				'thumbnail_id_save' => false,
				'cache'             => true,
				'before'            => '',
				'after'             => '',
			)
		) );

		if ( empty( $args['post_id'] ) ) {
			return false;
		}

		if ( 'array' === $args['format'] ) {
			$args['link_to_post'] = false;
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
