<?php
/**
 * A Helper class for retrieving images.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

class CareLib_Image_Grabber {

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Property for storing images which have been grabbed.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected static $images = array();

	/**
	 * Property for storing the cache key.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected static $cache_key = '';

	/**
	 * Property for storing the image cache.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected static $cache = array();

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	protected function setup_args( $args ) {
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

		return $args;
	}

	/**
	 * Setup object caching if it's enabled.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display an image.
	 * @return bool true if cache has been set up, false otherwise
	 */
	protected function setup_cache( $args ) {
		if ( $args['cache'] ) {
			self::$cache_key = md5( serialize( compact( array_keys( $args ) ) ) );
			self::$cache     = (array) wp_cache_get( $args['post_id'], "{$this->prefix}_image_grabber" );
			return true;
		}
		return false;
	}

	/**
	 * Get a cached image.
	 *
	 * Uses object cache if it's available and enabled and falls back to a
	 * stored image value to prevent multiple searches.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int $post_id the post ID associated with the image to get
	 * @return string|bool false if no cached image is found
	 */
	protected function get_cache( $post_id ) {
		if ( ! empty( self::$cache[ self::$cache_key ] ) ) {
			return self::$cache[ self::$cache_key ];
		} elseif ( ! empty( self::$images[ $post_id ] ) ) {
			return self::$images[ $post_id ];
		}
		return false;
	}

	/**
	 * Set a cached image.
	 *
	 * Uses object cache if it's available and always stores an image value to
	 * prevent multiple searches.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display an image.
	 * @param  string $html the formatted HTML of a grabbed image to save.
	 * @return void
	 */
	protected function set_cache( $html, $args ) {
		self::$images[ $args['post_id'] ] = $html;

		if ( $args['cache'] ) {
			wp_cache_set(
				$args['post_id'],
				array( self::$cache_key => $html ),
				"{$this->prefix}_image_grabber"
			);
		}
	}

	/**
	 * Search the content are for an image to grab. Use cache if it's available.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display an image.
	 * @return bool|array $image a grabbed image properties or false if no image is found
	 */
	protected function find_the_image( $args ) {
		$this->setup_cache( $args );

		if ( $cache = $this->get_cache( $args['post_id'] ) ) {
			return $cache;
		}

		if ( ! $image = $this->get_image_by( $args ) ) {
			return false;
		}

		if ( ! empty( $meta_key_save ) ) {
			$this->get_meta_key_save( $args, $image );
		}

		$this->set_cache( $this->format_image( $args, $image ), $args );

		return apply_filters( "{$this->prefix}_image_grabber_image", $image );
	}

	/**
	 * Grab the image using a pre-defined order of available methods.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display an image.
	 * @return bool|array $image a grabbed image properties or false if no image is found
	 */
	protected function get_image_by( $args ) {
		if ( ! empty( $args['meta_key'] ) ) {
			if ( $image = $this->get_by_meta_key( $args ) ) {
				return $image;
			}
		}

		if ( ! empty( $args['featured'] ) ) {
			if ( $image = $this->get_by_featured_image( $args ) ) {
				return $image;
			}
		}

		if ( ! empty( $args['attachment'] ) ) {
			if ( $image = $this->get_by_attachment( $args ) ) {
				return $image;
			}
		}

		if ( ! empty( $args['default_image'] ) ) {
			if ( $image = $this->get_by_default( $args ) ) {
				return $image;
			}
		}

		return false;
	}

	/**
	 * Return the raw attributes of a grabbed image.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $html the formatted HTML of a grabbed image
	 * @return array $output the raw attributes of a grabbed image
	 */
	protected function get_raw_image( $html ) {
		$output = array();

		foreach ( wp_kses_hair( $html, array( 'http', 'https' ) ) as $attr ) {
			$output[ $attr['name'] ] = $attr['value'];
		}

		return $output;
	}

	/**
	 * Return a sanitized string of html classes.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $classes a raw array of html classes to be sanitized.
	 * @return array a sanitized array of lowercase html class values.
	 */
	protected function sanitize_classes( $classes ) {
		$classes = array_map( 'strtolower', $classes );
		return array_map( 'sanitize_html_class', $classes );
	}

	/**
	 * Build a sanitized string of html classes for our grabbed image.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string a formatted string of sanitized HTML classes.
	 */
	protected function build_image_classes( $args, $image ) {
		$classes = array(
			$image['height'] > $image['width'] ? 'portrait' : 'landscape',
			$args['size'],
			$args['image_class'],
		);

		foreach ( (array) $args['meta_key'] as $key ) {
			$classes[] = $key;
		}

		return trim( join( ' ', array_unique( $this->sanitize_classes( $classes ) ) ) );
	}

	/**
	 * Return a formatted image size attribute.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $size the size attribute to format.
	 * @param  string $type the type of attribute being formatted (height or width)
	 * @return string a formatted image size attribute of height or width.
	 */
	protected function format_size( $size, $type ) {
		return empty( $size ) ? '' : ' ' . esc_attr( $type ) . '="' . esc_attr( $size ) . '"';
	}

	/**
	 * Return a formatted html class attribute.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $class the class attribute to format.
	 * @return string a formatted html class.
	 */
	protected function format_class( $class ) {
		return empty( $class ) ? '' : ' class="' . sanitize_html_class( $class ) . '"';
	}

	/**
	 * Return a formatted image srcset attribute.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $srcset the array of srcset values to format.
	 * @return string a formatted html srcset attribute.
	 */
	protected function format_srcset( $srcset ) {
		return empty( $srcset ) ? '' : sprintf( ' srcset="%s"', esc_attr( join( ', ', $srcset ) ) );
	}

	/**
	 * Wrap a formatted <img> with a link to the associated post if the
	 * argument has been set.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string $image Formatted image markup.
	 */
	protected function maybe_add_link_wrapper( $html, $args ) {
		if ( ! $args['link_to_post'] ) {
			return $html;
		}
		return sprintf( '<a href="%s"%s title="%s">%s</a>',
			get_permalink( $args['post_id'] ),
			$this->format_class( $args['link_class'] ),
			esc_attr( apply_filters( 'the_title', get_post_field( 'post_title', $args['post_id'] ) ) ),
			$html
		);
	}

	/**
	 * Return a formatted <img> string.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string $image Formatted image markup.
	 */
	protected function format_image_html( $args, $image ) {
		$image_alt = apply_filters( 'the_title', get_post_field( 'post_title', $args['post_id'] ) );
		if ( ! empty( $image['alt'] ) ) {
			$image_alt = $image['alt'];
		}

		$html = sprintf( '<img src="%s" alt="%s" class="%s" %s%s%s />',
			$image['src'],
			wp_strip_all_tags( $image_alt, true ),
			$this->build_image_classes( $args, $image ),
			$this->format_srcset( $image['srcset'] ),
			$this->format_size( $image['width'], 'width' ),
			$this->format_size( $image['height'], 'height' )
		);

		return $this->maybe_add_link_wrapper( $html, $args );
	}

	/**
	 * Apply the post_thumbnail_html filter if a given image has a thumbnail id.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  string $html a formatted <img> string.
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string $image Formatted image markup.
	 */
	protected function maybe_add_thumbnail_html( $html, $image, $args ) {
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
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string $image Formatted image (w/link to post if the option is set).
	 */
	protected function format_image( $args, $image ) {
		if ( empty( $image['src'] ) ) {
			return false;
		}
		return $this->maybe_add_thumbnail_html(
			$this->format_image_html( $args, $image ),
			$image,
			$args
		);
	}

	/**
	 * Get image by custom field key.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_meta_key( $args ) {
		foreach ( (array) $args['meta_key'] as $meta_key ) {

			$image = get_post_meta( $args['post_id'], $meta_key, true );

			if ( ! empty( $image ) ) {
				break;
			}
		}

		return empty( $image ) ? false : array( 'src' => $image, 'url' => $image );
	}

	/**
	 * Get the featured image (i.e., WP's post thumbnail).
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_featured_image( $args ) {
		$id = get_post_thumbnail_id( $args['post_id'] );

		if ( empty( $id ) ) {
			return false;
		}

		$args['size'] = apply_filters( 'post_thumbnail_size', $args['size'] );

		return $this->get_image_attachment( $id, $args );
	}

	/**
	 * Check for attachment images.
	 *
	 * Uses get_children() to check if the post has images attached.  If image
	 * attachments are found, loop through each.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_attachment( $args ) {
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

		return $this->get_image_attachment( $id, $args );
	}

	/**
	 * Adds an array of srcset image sources and descriptors based on the
	 * `srcset_sizes` argument.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int $id
	 * @return void
	 */
	protected function get_srcset( $id ) {
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
	 * @since  0.2.0
	 * @access protected
	 * @param  int $id
	 * @return void
	 */
	protected function get_image_attachment( $id, $args ) {
		if ( false === $id ) {
			return false;
		}

		// Get the attachment image.
		$image = wp_get_attachment_image_src( $id, $args['size'] );
		$alt   = get_post_meta( $id, '_wp_attachment_image_alt', true );

		// Save the attachment as the 'featured image'.
		if ( true === $args['thumbnail_id_save'] ) {
			$this->thumbnail_id_save( $id );
		}

		return empty( $image ) ? false : array(
			'id'      => $id,
			'src'     => $image[0],
			'width'   => $image[1],
			'height'  => $image[2],
			'alt'     => trim( wp_strip_all_tags( $alt, true ) ),
			'caption' => get_post_field( 'post_excerpt', $id ),
			'srcset'  => $this->get_srcset( $id, $image ),
		);
	}

	/**
	 * Set a default image.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_default( $args ) {
		return array(
			'src' => $args['default_image'],
			'url' => $args['default_image'],
		);
	}

	/**
	 * Save the image URL as the value of the meta key provided.
	 *
	 * This allows users to set a custom meta key for their image. By doing
	 * this, users can trim off database queries when grabbing attachments.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  array $image Array of image attributes ($image, $classes, $alt, $caption).
	 */
	protected function get_meta_key_save( $args, $image ) {
		if ( empty( $args['meta_key_save'] ) || empty( $image['src'] ) ) {
			return;
		}

		$meta = get_post_meta( $args['post_id'], $args['meta_key_save'], true );

		if ( $meta === $image['src'] ) {
			return false;
		}

		update_post_meta( $args['post_id'], $args['meta_key_save'], $image['src'], $meta );
	}

}
