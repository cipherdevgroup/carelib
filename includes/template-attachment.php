<?php
/**
 * Template helper functions used within the attachment pages.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      1.0.0
 */

/**
 * Output a formatted attachment image.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_attachment_image() {
	if ( ! wp_attachment_is_image() ) {
		return false;
	}

	$image = wp_get_attachment_image(
		get_the_ID(),
		'full',
		false,
		array( 'class' => 'aligncenter' )
	);

	if ( has_excerpt() ) {
		$src   = wp_get_attachment_image_src( get_the_ID(), 'full' );
		$image = img_caption_shortcode(
			array(
				'align'   => 'aligncenter',
				'width'   => esc_attr( $src[1] ),
				'caption' => get_the_excerpt(),
			),
			wp_get_attachment_image( get_the_ID(), 'full', false )
		);
	}

	return apply_filters( 'carelib_carelib_attachment_image', $image );
}

/**
 * Output a formatted attachment image.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function carelib_attachment_image() {
	echo carelib_get_attachment_image();
}

/**
 * Output a formatted WordPress image gallery of related attachments on
 * attachment image pages.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function carelib_get_attachment_image_gallery() {
	if ( ! wp_attachment_is_image() ) {
		return false;
	}

	$parent = get_queried_object()->post_parent;

	if ( empty( $parent ) ) {
		return false;
	}

	$gallery = gallery_shortcode( array(
		'columns'     => 4,
		'numberposts' => 8,
		'orderby'     => 'rand',
		'id'          => $parent,
		'exclude'     => get_the_ID(),
	) );

	if ( empty( $gallery ) ) {
		return false;
	}

	$markup = '<div class="image-gallery"><h3 class="attachment-meta-title">%s</h3>%s</div>';
	$title = esc_attr__( 'Related Images', 'carelib' );
	$output = sprintf( $markup, $title, $gallery );

	return apply_filters( 'carelib_attachment_image_gallery', $output, $markup, $title, $gallery );
}
