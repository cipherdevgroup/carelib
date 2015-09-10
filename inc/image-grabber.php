<?php
/**
 * A Helper class for retrieving images.
 *
 * Based on Get the Image by Justin Tadlock.
 *
 * @package   CareLib
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @author    Robert Neu <rob@wpsitecare.com>
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
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @uses   add_action
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'save_post',         array( $this, 'delete_cache_by_post' ), 10 );
		add_action( 'deleted_post_meta', array( $this, 'delete_cache_by_meta' ), 10, 2 );
		add_action( 'updated_post_meta', array( $this, 'delete_cache_by_meta' ), 10, 2 );
		add_action( 'added_post_meta',   array( $this, 'delete_cache_by_meta' ), 10, 2 );
	}

	/**
	 * Get a post image in a specified way and either return or echo it.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $args Arguments for how to load and display the image.
	 * @param  bool $echo true to echo, false to return.
	 * @return string|array The HTML for the image. | Image attributes in an array.
	 */
	public function grab_the_image( $args = array(), $echo = true ) {
		$args = wp_parse_args( $args, apply_filters( "{$this->prefix}_image_grabber_defaults",
			array(
				'meta_key'          => array( 'Thumbnail', 'thumbnail' ),
				'post_id'           => get_the_ID(),
				'attachment'        => true,
				'featured'          => true,
				'size'              => 'thumbnail',
				'default_image'     => false,
				'order'             => array( 'meta_key', 'featured', 'attachment', 'default' ),
				'link_to_post'      => true,
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

		// Bail if there's no post_id for some reason.
		if ( empty( $args['post_id'] ) ) {
			return false;
		}

		if ( 'array' === $args['format'] ) {
			$args['link_to_post'] = false;
			$echo = false;
		}

		$image = apply_filters( "{$this->prefix}_image_grabber",
			$this->search_content( $args )
		);

		if ( ! $echo ) {
			return $this->get_image( $image, $args );
		}

		$this->image( $image, $args );
	}

	function search_content( $args ) {
		$key   = md5( serialize( compact( array_keys( $args ) ) ) );
		$cache = (array) wp_cache_get( $args['post_id'], "{$this->prefix}_image_grabber" );

		if ( ! empty( $cache[ $key ] ) ) {
			return $cache[ $key ];
		}

		$image = '';

		if ( ! empty( $args['meta_key'] ) ) {
			$image = $this->get_by_meta_key( $args );
		}

		if ( empty( $image ) && ! empty( $args['featured'] ) ) {
			$image = $this->get_by_post_thumbnail( $args );
		}

		if ( empty( $image ) && ! empty( $args['attachment'] ) ) {
			$image = $this->get_by_attachment( $args );
		}

		if ( empty( $image ) && ! empty( $args['default_image'] ) ) {
			$image = $this->get_by_default( $args );
		}

		if ( empty( $image ) ) {
			return false;
		}

		if ( ! empty( $meta_key_save ) ) {
			$this->get_meta_key_save( $args, $image );
		}

		$html = $this->get_format( $args, $image );

		$cache[ $key ] = $html;
		wp_cache_set( $args['post_id'], $cache, "{$this->prefix}_image_grabber" );

		return $image;
	}

	protected function get_image( $image, $args ) {
		$html = $this->get_format( $args, $image );

		if ( 'array' === $args['format'] ) {
			$out = array();

			$atts = wp_kses_hair( $html, array( 'http', 'https' ) );

			foreach ( $atts as $att ) {
				$out[ $att['name'] ] = $att['value'];
			}

			return $out;
		}

		return ! empty( $html ) ? $args['before'] . $html . $args['after'] : $image;
	}

	protected function image( $image, $args ) {
		if ( 'array' === $args['format'] ) {
			return;
		}
		if ( isset( $image['post_thumbnail_id'] ) ) {
			do_action( 'begin_fetch_post_thumbnail_html',
				$args['post_id'],
				$image['post_thumbnail_id'],
				$args['size']
			);
		}

		echo $this->get_image( $image, $args );

		if ( isset( $image['post_thumbnail_id'] ) ) {
			do_action( 'end_fetch_post_thumbnail_html',
				$args['post_id'],
				$image['post_thumbnail_id'],
				$args['size']
			);
		}
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
	protected function get_by_post_thumbnail( $args ) {
		$id = get_post_thumbnail_id( $args['post_id'] );

		if ( empty( $id ) ) {
			return false;
		}

		$image = wp_get_attachment_image_src(
			$id,
			apply_filters( 'post_thumbnail_size', $args['size'] )
		);

		return empty( $image ) ? false : array(
			'src' => $image[0],
			'url' => $image[0],
			'post_thumbnail_id' => $id,
			'alt' => trim( strip_tags( get_post_field( 'post_excerpt', $id ) ) ),
		);
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
	protected function get_by_attachment( $args = array() ) {
		$post_type = get_post_type( $args['post_id'] );

		if ( 'attachment' === $post_type && wp_attachment_is_image( $args['post_id'] ) ) {
			$attachment_id = $args['post_id'];
		} elseif ( 'attachment' !== $post_type ) {

			$attachments = get_children(
				array(
					'post_parent'      => $args['post_id'],
					'post_status'      => 'inherit',
					'post_type'        => 'attachment',
					'post_mime_type'   => 'image',
					'order'            => 'ASC',
					'orderby'          => 'menu_order ID',
					'suppress_filters' => true,
				)
			);

			if ( ! empty( $attachments ) ) {
				$i = 0;
				foreach ( $attachments as $id => $attachment ) {
					$attachment_id = $id;
					if ( ++$i === $args['order_of_image'] ) {
						break;
					}
				}
			}
		}

		if ( empty( $attachment_id ) ) {
			return false;
		}

		$image = wp_get_attachment_image_src( $attachment_id, $args['size'] );

		$alt = trim( strip_tags( get_post_field( 'post_excerpt', $attachment_id ) ) );

		if ( true === $args['thumbnail_id_save'] ) {
			set_post_thumbnail( $args['post_id'], $attachment_id );
		}

		return array( 'src' => $image[0], 'url' => $image[0], 'alt' => $alt );
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

	protected function format_classes( $classes, $meta_key, $size, $image_class ) {
		if ( is_array( $meta_key ) ) {
			foreach ( $meta_key as $key ) {
				$classes[] = sanitize_html_class( $key );
			}
		}

		$classes[] = sanitize_html_class( $size );
		$classes[] = sanitize_html_class( $image_class );

		return join( ' ', array_unique( $classes ) );
	}

	protected function format_size( $size, $type ) {
		return empty( $size ) ? '' : ' ' . esc_attr( $type ) . '="' . esc_attr( $size ) . '"';
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
	protected function get_format( $args, $image ) {
		if ( empty( $image['src'] ) ) {
			return false;
		}

		extract( $args );

		$title_attr = apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) );
		$image_alt  = $title_attr;

		if ( ! empty( $image['alt'] ) ) {
			$image_alt = $image['alt'];
		}

		$html = sprintf( '<img src="%s" alt="%s" class="%s" %s %s />',
			$image['src'],
			wp_strip_all_tags( $image_alt, true ),
			$this->format_classes( $classes, $meta_key, $size, $image_class ),
			$this->format_size( $width, 'width' ),
			$this->format_size( $height, 'height' )
		);

		if ( $link_to_post ) {
			$html = sprintf( '<a href="%s" title="%s">%s</a>',
				get_permalink( $post_id ),
				esc_attr( $title_attr ),
				$html
			);
		}

		if ( ! empty( $image['post_thumbnail_id'] ) ) {
			$html = apply_filters( 'post_thumbnail_html', $html, $post_id, $image['post_thumbnail_id'], $size, '' );
		}

		return $html;
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

	/**
	 * Deletes the image cache for the specific post when the 'save_post' hook
	 * is fired.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int $post_id The ID of the post to delete the cache for.
	 * @return void
	 */
	public function delete_cache_by_post( $post_id ) {
		wp_cache_delete( $post_id, "{$this->prefix}_image_grabber" );
	}

	/**
	 * Deletes the image cache for a specific post when the 'added_post_meta',
	 * 'deleted_post_meta', or 'updated_post_meta' hooks are called.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @param  int $meta_id The ID of the metadata being updated.
	 * @param  int $post_id The ID of the post to delete the cache for.
	 * @return void
	 */
	public function delete_cache_by_meta( $meta_id, $post_id ) {
		wp_cache_delete( $post_id, "{$this->prefix}_image_grabber" );
	}

}
