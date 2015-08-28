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

/**
 * Class for getting images related to a post.
 *
 * @since  0.2.0
 * @access public
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
		add_theme_support( 'post-thumbnails' );
		$this->wp_hooks();
		//self::image_hooks();
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
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @uses   add_action
	 * @return void
	 */
	protected function image_hooks() {
		global $wp_embed;
		add_filter( "{$this->prefix}_image_grabber_post_content", array( $wp_embed, 'run_shortcode' ) );
		add_filter( "{$this->prefix}_image_grabber_post_content", array( $wp_embed, 'autoembed' ) );
	}

	/**
	 * The main image function for displaying an image.  It supports several
	 * arguments that allow developers to customize how the script outputs the
	 * image.
	 *
	 * The image check order is important to note here.  If an image is found by any specific check, the script
	 * will no longer look for images.  The check order is 'meta_key', 'the_post_thumbnail', 'attachment',
	 * 'image_scan', 'callback', and 'default_image'.
	 *
	 * @since 0.2.0
	 * @access public
	 * @global $post The current post's database object.
	 * @param array $args Arguments for how to load and display the image.
	 * @return string|array The HTML for the image. | Image attributes in an array.
	 */
	public function grab_the_image( $args = array(), $echo = true ) {
		$defaults = apply_filters( "{$this->prefix}_image_grabber_defaults", array(
			'meta_key'           => array( 'Thumbnail', 'thumbnail' ), // array|string
			'post_id'            => get_the_ID(),
			'attachment'         => true,
			'the_post_thumbnail' => true, // WP 2.9+ image function
			'size'               => 'thumbnail',
			'default_image'      => false,
			'order_of_image'     => 1,
			'link_to_post'       => true,
			'image_class'        => false,
			'image_scan'         => false,
			'width'              => false,
			'height'             => false,
			'format'             => 'img',
			'meta_key_save'      => false,
			'thumbnail_id_save'  => false, // Set 'featured image'.
			'callback'           => null,
			'cache'              => true,
			'before'             => '',
			'after'              => '',
		) );

		$args = wp_parse_args( $args, $defaults );

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

		/* If no image found and $the_post_thumbnail is set to true, check for a post image (WP feature). */
		if ( empty( $image ) && ! empty( $args['the_post_thumbnail'] ) ) {
			$image = $this->get_by_post_thumbnail( $args );
		}

		/* If no image found and $attachment is set to true, check for an image by attachment. */
		if ( empty( $image ) && ! empty( $args['attachment'] ) ) {
			$image = $this->get_by_attachment( $args );
		}

		/* If no image found and $image_scan is set to true, scan the post for images. */
		if ( empty( $image ) && ! empty( $args['image_scan'] ) ) {
			$image = $this->get_by_scan( $args );
		}

		/* If no image found and a callback function was given. Callback function must pass back array of <img> attributes. */
		if ( empty( $image ) && ! is_null( $args['callback'] ) && function_exists( $args['callback'] ) ) {
			$image = call_user_func( $callback, $args );
		}

		/* If no image found and a $default_image is set, get the default image. */
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
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_meta_key( $args = array() ) {
		foreach ( (array) $args['meta_key'] as $meta_key ) {

			$image = get_post_meta( $args['post_id'], $meta_key, true );

			if ( ! empty( $image ) ) {
				break;
			}
		}

		return empty( $image ) ? false : array( 'src' => $image, 'url' => $image );
	}

	/**
	 * Checks for images using a custom version of the WordPress 2.9+ get_the_post_thumbnail() function.
	 * If an image is found, return it and the $post_thumbnail_id.  The WordPress function's other filters are
	 * later added in the display_the_image() function.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_post_thumbnail( $args = array() ) {
		$id = get_post_thumbnail_id( $args['post_id'] );

		if ( empty( $id ) ) {
			return false;
		}

		$size = apply_filters( 'post_thumbnail_size', $args['size'] );

		if ( ! $image = wp_get_attachment_image_src( $id, $size ) ) {
			return false;
		}

		$alt = trim( strip_tags( get_post_field( 'post_excerpt', $id ) ) );

		return array( 'src' => $image[0], 'url' => $image[0], 'post_thumbnail_id' => $id, 'alt' => $alt );
	}

	/**
	 * Check for attachment images.  Uses get_children() to check if the post has images attached.  If image
	 * attachments are found, loop through each.  The loop only breaks once $order_of_image is reached.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_attachment( $args = array() ) {
		$post_type = get_post_type( $args['post_id'] );

		/* Check if the post itself is an image attachment. */
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

			/* Check if any attachments were found. */
			if ( ! empty( $attachments ) ) {

				/* Set the default iterator to 0. */
				$i = 0;

				/* Loop through each attachment. */
				foreach ( $attachments as $id => $attachment ) {

					/* Set the attachment ID as the current ID in the loop. */
					$attachment_id = $id;

					/* Break if/when we hit 'order_of_image'. */
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
	 * Scans the post for images within the content.  Not called by default with $this->get().  Shouldn't use
	 * if using large images within posts, better to use the other options.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_scan( $args = array() ) {

		/* Search the post's content for the <img /> tag and get its URL. */
		preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $args['post_id'] ), $matches );

		/* If there is a match for the image, return its URL. */
		if ( isset( $matches ) && ! empty( $matches[1][0] ) ) {
			return array( 'src' => $matches[1][0], 'url' => $matches[1][0] );
		}

		return false;
	}

	/**
	 * Used for setting a default image.  The function simply returns the image URL it was given in an array.
	 * Not used with $this->get() by default.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_default( $args = array() ) {
		return array( 'src' => $args['default_image'], 'url' => $args['default_image'] );
	}

	/**
	 * Formats an image with appropriate alt text and class.  Adds a link to the post if argument is set.  Should
	 * only be called if there is an image to display, but will handle it if not.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @param array $image Array of image attributes ($image, $classes, $alt, $caption).
	 * @return string $image Formatted image (w/link to post if the option is set).
	 */
	protected function get_format( $args = array(), $image = false ) {
		if ( empty( $image['src'] ) ) {
			return false;
		}

		/* Extract the arguments for easy-to-use variables. */
		extract( $args );

		/* If there is alt text, set it.  Otherwise, default to the post title. */
		$image_alt = ( ( ! empty( $image['alt'] ) ) ? $image['alt'] : apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) ) );

		/* If there is a width or height, set them as HMTL-ready attributes. */
		$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
		$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );

		/* Loop through the custom field keys and add them as classes. */
		if ( is_array( $meta_key ) ) {
			foreach ( $meta_key as $key ) {
				$classes[] = sanitize_html_class( $key );
			}
		}

		/* Add the $size and any user-added $image_class to the class. */
		$classes[] = sanitize_html_class( $size );
		$classes[] = sanitize_text_field( $image_class );

		/* Join all the classes into a single string and make sure there are no duplicates. */
		$class = join( ' ', array_unique( $classes ) );

		/* Add the image attributes to the <img /> element. */
		$html = '<img src="' . $image['src'] . '" alt="' . esc_attr( strip_tags( $image_alt ) ) . '" class="' . esc_attr( $class ) . '"' . $width . $height . ' />';

		/* If $link_to_post is set to true, link the image to its post. */
		if ( $link_to_post ) {
			$html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) ) ) . '">' . $html . '</a>';
		}

		/* If there is a $post_thumbnail_id, apply the WP filters normally associated with get_the_post_thumbnail(). */
		if ( ! empty( $image['post_thumbnail_id'] ) ) {
			$html = apply_filters( 'post_thumbnail_html', $html, $post_id, $image['post_thumbnail_id'], $size, '' );
		}
		return $html;
	}

	/**
	 * Saves the image URL as the value of the meta key provided.  This allows users to set a custom meta key
	 * for their image.  By doing this, users can trim off database queries when grabbing attachments or get rid
	 * of expensive scans of the content when using the image scan feature.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @param array $image Array of image attributes ($image, $classes, $alt, $caption).
	 */
	protected function get_meta_key_save( $args = array(), $image = array() ) {
		if ( empty( $args['meta_key_save'] ) || empty( $image['src'] ) ) {
			return;
		}

		/* Get the current value of the meta key. */
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
	 * @param  int      $post_id  The ID of the post to delete the cache for.
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
