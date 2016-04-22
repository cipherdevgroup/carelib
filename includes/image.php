<?php
/**
 * A Helper class for retrieving images.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Return a grabbed image.
 *
 * @since  1.0.0
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
function carelib_get_image( $args = array() ) {
	global $carelib_prefix;

	$args = wp_parse_args( $args, apply_filters( "{$carelib_prefix}_image_defaults",
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

	$key = md5( serialize( compact( array_keys( $args ) ) ) );

	$cache = (array) wp_cache_get( $args['post_id'], "{$carelib_prefix}_image" );

	if ( ! isset( $cache[ $key ] ) || empty( $cache ) ) {

		$image = _carelib_image_find( $args );

		if ( $image ) {

			if ( is_array( $image ) ) {
				$image = _carelib_image_format_image( $args, $image );
			}

			$cache[ $key ] = $image;

			wp_cache_set( $args['post_id'], $cache, "{$carelib_prefix}_image" );
		}
	} else {
		$image = $cache[ $key ];
	}

	if ( 'array' === $args['format'] ) {
		return _carelib_image_get_raw( $image );
	}

	return empty( $image ) ? false : "{$args['before']}{$image}{$args['after']}";
}

/**
 * Search the content are for an image to grab. Use cache if it's available.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display an image.
 * @return bool|array $image a grabbed image properties or false if no image is found
 */
function _carelib_image_find( $args ) {
	if ( ! $image = _carelib_image_get_by( $args ) ) {
		return false;
	}

	if ( ! empty( $args['meta_key_save'] ) ) {
		_carelib_image_get_meta_key_save( $args, $image );
	}

	return apply_filters( "{$GLOBALS['carelib_prefix']}_image", $image );
}

/**
 * Grab the image using a pre-defined order of available methods.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display an image.
 * @return bool|array $image a grabbed image properties or false if no image is found
 */
function _carelib_image_get_by( $args ) {
	if ( ! empty( $args['meta_key'] ) ) {
		if ( $image = _carelib_image_get_by_meta_key( $args ) ) {
			return $image;
		}
	}

	if ( ! empty( $args['featured'] ) ) {
		if ( $image = _carelib_image_get_by_featured_image( $args ) ) {
			return $image;
		}
	}

	if ( ! empty( $args['attachment'] ) ) {
		if ( $image = _carelib_image_get_by_attachment( $args ) ) {
			return $image;
		}
	}

	if ( ! empty( $args['default_image'] ) ) {
		if ( $image = _carelib_image_get_by_default( $args ) ) {
			return $image;
		}
	}

	return false;
}

/**
 * Return the raw attributes of a grabbed image.
 *
 * @since  1.0.0
 * @access protected
 * @param  string $html the formatted HTML of a grabbed image
 * @return array $output the raw attributes of a grabbed image
 */
function _carelib_image_get_raw( $html ) {
	if ( empty( $html ) ) {
		return false;
	}
	$output = array();

	foreach ( wp_kses_hair( (string) $html, array( 'http', 'https' ) ) as $attr ) {
		$output[ $attr['name'] ] = $attr['value'];
	}

	return empty( $output ) ? false : $output;
}

/**
 * Return a sanitized string of html classes.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $classes a raw array of html classes to be sanitized.
 * @return array a sanitized array of lowercase html class values.
 */
function _carelib_image_sanitize_classes( $classes ) {
	$classes = array_map( 'strtolower', $classes );
	return array_map( 'sanitize_html_class', $classes );
}

function _carelib_image_get_size( $args, $image, $type ) {
	return isset( $image[ $type ] ) ? $image[ $type ] : $args[ $type ];
}

/**
 * Build a sanitized string of html classes for our grabbed image.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string a formatted string of sanitized HTML classes.
 */
function _carelib_image_build_classes( $args, $image ) {
	$format = 'landscape';
	if ( _carelib_image_get_size( $args, $image, 'height' ) > _carelib_image_get_size( $args, $image, 'width' ) ) {
		$format = 'portrait';
	}
	$classes = array(
		$format,
		$args['size'],
		$args['image_class'],
	);

	foreach ( (array) $args['meta_key'] as $key ) {
		$classes[] = $key;
	}

	return trim( join( ' ', array_unique( _carelib_image_sanitize_classes( $classes ) ) ) );
}

/**
 * Return a formatted image size attribute.
 *
 * @since  1.0.0
 * @access protected
 * @param  string $size the size attribute to format.
 * @param  string $type the type of attribute being formatted (height or width)
 * @return string a formatted image size attribute of height or width.
 */
function _carelib_image_format_size( $args, $image, $type ) {
	$size = _carelib_image_get_size( $args, $image, $type );
	return empty( $size ) ? '' : ' ' . esc_attr( $type ) . '="' . esc_attr( $size ) . '"';
}

/**
 * Return a formatted html class attribute.
 *
 * @since  1.0.0
 * @access protected
 * @param  string $class the class attribute to format.
 * @return string a formatted html class.
 */
function _carelib_image_format_class( $class ) {
	return empty( $class ) ? '' : ' class="' . sanitize_html_class( $class ) . '"';
}

/**
 * Return a formatted image srcset attribute.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $srcset the array of srcset values to format.
 * @return string a formatted html srcset attribute.
 */
function _carelib_image_format_srcset( $image ) {
	return empty( $image['srcset'] ) ? '' : sprintf( ' srcset="%s"', esc_attr( join( ', ', $image['srcset'] ) ) );
}

/**
 * Wrap a formatted <img> with a link to the associated post if the
 * argument has been set.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string $image Formatted image markup.
 */
function _carelib_image_maybe_add_link_wrapper( $html, $args ) {
	if ( ! $args['link_to_post'] ) {
		return $html;
	}
	return sprintf( '<a href="%s"%s>%s</a>',
		get_permalink( $args['post_id'] ),
		_carelib_image_format_class( $args['link_class'] ),
		$html
	);
}

/**
 * Return a formatted <img> string.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string $image Formatted image markup.
 */
function _carelib_image_format_image_html( $args, $image ) {
	$image_alt = '';

	if ( ! empty( $image['alt'] ) ) {
		$image_alt = $image['alt'];
	} else {
		$image_alt = get_the_title( $args['post_id'] );
	}

	if ( isset( $image['post_thumbnail_id'] ) ) {
		do_action( 'begin_fetch_post_thumbnail_html',
			$args['post_id'],
			$image['post_thumbnail_id'],
			$args['size']
		);
	}

	$html = sprintf( '<img src="%s" alt="%s" class="%s" %s%s%s />',
		$image['src'],
		esc_attr( $image_alt, true ),
		_carelib_image_build_classes( $args, $image ),
		_carelib_image_format_srcset( $image ),
		_carelib_image_format_size( $args, $image, 'width' ),
		_carelib_image_format_size( $args, $image, 'height' )
	);

	if ( isset( $image['post_thumbnail_id'] ) ) {
		do_action( 'end_fetch_post_thumbnail_html',
			$args['post_id'],
			$image['post_thumbnail_id'],
			$args['size']
		);
	}

	return _carelib_image_maybe_add_link_wrapper( $html, $args );
}

/**
 * Apply the post_thumbnail_html filter if a given image has a thumbnail id.
 *
 * @since  1.0.0
 * @access protected
 * @param  string $html a formatted <img> string.
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string $image Formatted image markup.
 */
function _carelib_image_maybe_add_thumbnail_html( $html, $image, $args ) {
	if ( empty( $image['post_thumbnail_id'] ) ) {
		return $html;
	}
	return apply_filters( 'post_thumbnail_html', $html,
		$args['post_id'],
		$image['post_thumbnail_id'],
		$args['size'],
		''
	);
}

/**
 * Format an image with appropriate alt text and class. Adds a link if the
 * argument is set.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 * @return string $image Formatted image (w/link to post if the option is set).
 */
function _carelib_image_format_image( $args, $image ) {
	if ( empty( $image['src'] ) ) {
		return false;
	}
	return _carelib_image_maybe_add_thumbnail_html(
		_carelib_image_format_image_html( $args, $image ),
		$image,
		$args
	);
}

/**
 * Get image by custom field key.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function _carelib_image_get_by_meta_key( $args ) {
	foreach ( (array) $args['meta_key'] as $meta_key ) {

		$image = get_post_meta( $args['post_id'], $meta_key, true );

		if ( ! empty( $image ) ) {
			break;
		}
	}

	if ( ! empty( $image ) ) {
		if ( is_numeric( $image ) ) {
			return _carelib_image_get_attachment( $image, $args );
		}
		return array( 'src' => $image );
	}

	return false;
}

/**
 * Get the featured image (i.e., WP's post thumbnail).
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function _carelib_image_get_by_featured_image( $args ) {
	$id = get_post_thumbnail_id( $args['post_id'] );

	if ( empty( $id ) ) {
		return false;
	}

	$args['size'] = apply_filters( 'post_thumbnail_size', $args['size'] );

	return _carelib_image_get_attachment( $id, $args );
}

/**
 * Check for attachment images.
 *
 * Uses get_children() to check if the post has images attached.  If image
 * attachments are found, loop through each.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function _carelib_image_get_by_attachment( $args ) {
	$id = false;
	// Check if the post itself is an image attachment.
	if ( wp_attachment_is_image( $args['post_id'] ) ) {
		$id = $args['post_id'];
	} else {
		// Get attachments for the inputted $post_id.
		$attachments = get_children(
			array(
				'numberposts'      => 1,
				'post_parent'      => $args['post_id'],
				'post_status'      => 'inherit',
				'post_type'        => 'attachment',
				'post_mime_type'   => 'image',
				'order'            => 'ASC',
				'orderby'          => 'menu_order ID',
				'fields'           => 'ids',
			)
		);

		// Check if any attachments were found.
		if ( ! empty( $attachments ) ) {
			$id = array_shift( $attachments );
		}
	}

	return _carelib_image_get_attachment( $id, $args );
}

/**
 * Adds an array of srcset image sources and descriptors based on the
 * `srcset_sizes` argument.
 *
 * @since  1.0.0
 * @access protected
 * @param  int $id
 * @return void
 */
function _carelib_image_get_srcset( $id ) {
	if ( empty( $args['srcset_sizes'] ) ) {
		return false;
	}

	$srcsets = array();
	foreach ( $args['srcset_sizes'] as $size => $descriptor ) {

		$image = wp_get_attachment_image_src( $id, $size );

		// Make sure image doesn't match the image used for the `src` attribute.
		// This will happen often if the particular image size doesn't exist.
		if ( $image['src'] !== $image[0] ) {
			$srcsets[] = sprintf( '%s %s', esc_url( $image[0] ), esc_attr( $descriptor ) );
		}
	}

	return $srcsets;
}

/**
 * Get a WordPress image attachment.
 *
 * @since  1.0.0
 * @access protected
 * @param  int $id
 * @return void
 */
function _carelib_image_get_attachment( $id, $args ) {
	if ( false === $id ) {
		return false;
	}

	// Get the attachment image.
	$image = wp_get_attachment_image_src( $id, $args['size'] );
	$alt   = get_post_meta( $id, '_wp_attachment_image_alt', true );

	// Save the attachment as the 'featured image'.
	if ( true === $args['thumbnail_id_save'] ) {
		set_post_thumbnail( $args['post_id'], $id );
	}

	return empty( $image ) ? false : array(
		'id'      => $id,
		'src'     => $image[0],
		'width'   => $image[1],
		'height'  => $image[2],
		'alt'     => trim( esc_attr( $alt, true ) ),
		'caption' => get_post_field( 'post_excerpt', $id ),
		'srcset'  => _carelib_image_get_srcset( $id, $image ),
	);
}

/**
 * Set a default image.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @return array|bool Array of image attributes. | False if no image is found.
 */
function _carelib_image_get_by_default( $args ) {
	return array(
		'src' => $args['default_image'],
	);
}

/**
 * Save the image URL as the value of the meta key provided.
 *
 * This allows users to set a custom meta key for their image. By doing
 * this, users can trim off database queries when grabbing attachments.
 *
 * @since  1.0.0
 * @access protected
 * @param  array $args Arguments for how to load and display the image.
 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
 */
function _carelib_image_get_meta_key_save( $args, $image ) {
	if ( empty( $args['meta_key_save'] ) || empty( $image['src'] ) ) {
		return;
	}

	$meta = get_post_meta( $args['post_id'], $args['meta_key_save'], true );

	if ( $meta === $image['src'] ) {
		return false;
	}

	update_post_meta( $args['post_id'], $args['meta_key_save'], $image['src'], $meta );
}

/**
 * Delete the image cache.
 *
 * @since  1.0.0
 * @access protected
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function _carelib_delete_image_cache( $post_id ) {
	return wp_cache_delete( $post_id, "{$GLOBALS['carelib_prefix']}_image" );
}

/**
 * Delete the image cache for the specific post when the 'save_post' hook
 * is fired.
 *
 * @since  1.0.0
 * @access protected
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function carelib_delete_image_cache_by_post( $post_id ) {
	return _carelib_delete_image_cache( $post_id );
}

/**
 * Delete the image cache for a specific post when the 'added_post_meta',
 * 'deleted_post_meta', or 'updated_post_meta' hooks are called.
 *
 * @since  1.0.0
 * @access protected
 * @param  int $meta_id The ID of the metadata being updated.
 * @param  int $post_id The ID of the post to delete the cache for.
 * @return bool true when cache is deleted, false otherwise
 */
function carelib_delete_image_cache_by_meta( $meta_id, $post_id ) {
	return _carelib_delete_image_cache( $post_id );
}
