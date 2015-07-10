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
		self::wp_hooks();
	//	self::image_hooks();
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
	 * The main image function for displaying an image.  It supports several arguments that allow developers to
	 * customize how the script outputs the image.
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
		/* Set the default arguments. */
		$defaults = array(
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
		);

		/* Allow plugins/themes to filter the arguments. */
		$args = apply_filters( '$this->get_args', $args );

		/* Merge the input arguments and the defaults. */
		$args = wp_parse_args( $args, $defaults );

		/* If there's no post_id, we can'd do anything */
		if ( empty( $args['post_id'] ) ) {
			return false;
		}

		/* If $format is set to 'array', don't link to the post. */
		if ( 'array' === $args['format'] ) {
			$args['link_to_post'] = false;
		}

		/* Extract the array to allow easy use of variables. */
		extract( $args );

		/* Get cache key based on $args. */
		$key = md5( serialize( compact( array_keys( $args ) ) ) );

		/* Check for a cached image. */
		$image_cache = wp_cache_get( $post_id, '$this->get' );

		if ( !is_array( $image_cache ) )
			$image_cache = array();

		/* Set up a default, empty $image_html variable. */
		$image_html = '';

		/* If there is no cached image, let's see if one exists. */
		if ( !isset( $image_cache[ $key ] ) || empty( $cache ) ) {

			/* If a custom field key (array) is defined, check for images by custom field. */
			if ( !empty( $meta_key ) )
				$image = $this->get_by_meta_key( $args );

			/* If no image found and $the_post_thumbnail is set to true, check for a post image (WP feature). */
			if ( empty( $image ) && !empty( $the_post_thumbnail ) )
				$image = $this->get_by_post_thumbnail( $args );

			/* If no image found and $attachment is set to true, check for an image by attachment. */
			if ( empty( $image ) && !empty( $attachment ) )
				$image = $this->get_by_attachment( $args );

			/* If no image found and $image_scan is set to true, scan the post for images. */
			if ( empty( $image ) && !empty( $image_scan ) )
				$image = $this->get_by_scan( $args );

			/* If no image found and a callback function was given. Callback function must pass back array of <img> attributes. */
			if ( empty( $image ) && !is_null( $callback ) && function_exists( $callback ) )
				$image = call_user_func( $callback, $args );

			/* If no image found and a $default_image is set, get the default image. */
			if ( empty( $image ) && !empty( $default_image ) )
				$image = $this->get_by_default( $args );

			/* If an image was found. */
			if ( !empty( $image ) ) {

				/* If $meta_key_save was set, save the image to a custom field. */
				if ( !empty( $meta_key_save ) )
					$this->get_meta_key_save( $args, $image );

				/* Format the image HTML. */
				$image_html = $this->get_format( $args, $image );

				/* Set the image cache for the specific post. */
				$image_cache[ $key ] = $image_html;
				wp_cache_set( $post_id, $image_cache, '$this->get' );
			}
		}

		/* If an image was already cached for the post and arguments, use it. */
		else {
			$image_html = $image_cache[ $key ];
		}

		/* Allow plugins/theme to override the final output. */
		$image_html = apply_filters( '$this->get', $image_html );

		/* If $format is set to 'array', return an array of image attributes. */
		if ( 'array' === $format ) {

			/* Set up a default empty array. */
			$out = array();

			/* Get the image attributes. */
			$atts = wp_kses_hair( $image_html, array( 'http', 'https' ) );

			/* Loop through the image attributes and add them in key/value pairs for the return array. */
			foreach ( $atts as $att )
				$out[ $att['name'] ] = $att['value'];

			if ( isset( $out['src'] ) )
				$out['url'] = $out['src']; // @deprecated 0.5 Use 'src' instead of 'url'.

			/* Return the array of attributes. */
			return $out;
		}

		/* Or, if $echo is set to false, return the formatted image. */
		elseif ( false === $echo ) {
			return !empty( $image_html ) ? $args['before'] . $image_html . $args['after'] : $image_html;
		}

		/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
		if ( isset( $image['post_thumbnail_id'] ) )
			do_action( 'begin_fetch_post_thumbnail_html', $post_id, $image['post_thumbnail_id'], $size );

		/* Display the image if we get to this point. */
		echo !empty( $image_html ) ? $args['before'] . $image_html . $args['after'] : $image_html;

		/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
		if ( isset( $image['post_thumbnail_id'] ) )
			do_action( 'end_fetch_post_thumbnail_html', $post_id, $image['post_thumbnail_id'], $size );
	}

	/**
	 * Calls images by custom field key.  Script loops through multiple custom field keys.  If that particular
	 * key is found, $image is set and the loop breaks.  If an image is found, it is returned.
	 *
	 * @since 0.2.0
	 * @access protected
	 * @param array $args Arguments for how to load and display the image.
	 * @return array|bool Array of image attributes. | False if no image is found.
	 */
	protected function get_by_meta_key( $args = array() ) {

		/* If $meta_key is not an array. */
		if ( !is_array( $args['meta_key'] ) )
			$args['meta_key'] = array( $args['meta_key'] );

		/* Loop through each of the given meta keys. */
		foreach ( $args['meta_key'] as $meta_key ) {

			/* Get the image URL by the current meta key in the loop. */
			$image = get_post_meta( $args['post_id'], $meta_key, true );

			/* If an image was found, break out of the loop. */
			if ( !empty( $image ) )
				break;
		}

		/* If a custom key value has been given for one of the keys, return the image URL. */
		if ( !empty( $image ) )
			return array( 'src' => $image, 'url' => $image );

		return false;
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

		/* Check for a post image ID (set by WP as a custom field). */
		$post_thumbnail_id = get_post_thumbnail_id( $args['post_id'] );

		/* If no post image ID is found, return false. */
		if ( empty( $post_thumbnail_id ) )
			return false;

		/* Apply filters on post_thumbnail_size because this is a default WP filter used with its image feature. */
		$size = apply_filters( 'post_thumbnail_size', $args['size'] );

		/* Get the attachment image source.  This should return an array. */
		$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );

		if ( ! $image )
			return false;

		/* Get the attachment excerpt to use as alt text. */
		$alt = trim( strip_tags( get_post_field( 'post_excerpt', $post_thumbnail_id ) ) );

		/* Return both the image URL and the post thumbnail ID. */
		return array( 'src' => $image[0], 'url' => $image[0], 'post_thumbnail_id' => $post_thumbnail_id, 'alt' => $alt );
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

		/* Get the post type of the current post. */
		$post_type = get_post_type( $args['post_id'] );

		/* Check if the post itself is an image attachment. */
		if ( 'attachment' == $post_type && wp_attachment_is_image( $args['post_id'] ) ) {
			$attachment_id = $args['post_id'];
		}

		/* If the post is not an attachment, check if it has any image attachments. */
		elseif ( 'attachment' !== $post_type ) {

			/* Get attachments for the inputted $post_id. */
			$attachments = get_children(
				array(
					'post_parent'      => $args['post_id'],
					'post_status'      => 'inherit',
					'post_type'        => 'attachment',
					'post_mime_type'   => 'image',
					'order'            => 'ASC',
					'orderby'          => 'menu_order ID',
					'suppress_filters' => true
				)
			);

			/* Check if any attachments were found. */
			if ( !empty( $attachments ) ) {

				/* Set the default iterator to 0. */
				$i = 0;

				/* Loop through each attachment. */
				foreach ( $attachments as $id => $attachment ) {

					/* Set the attachment ID as the current ID in the loop. */
					$attachment_id = $id;

					/* Break if/when we hit 'order_of_image'. */
					if ( ++$i == $args['order_of_image'] )
						break;
				}
			}
		}

		/* Check if we have an attachment ID before proceeding. */
		if ( !empty( $attachment_id ) ) {

			/* Get the attachment image. */
			$image = wp_get_attachment_image_src( $attachment_id, $args['size'] );

			/* Get the attachment excerpt. */
			$alt = trim( strip_tags( get_post_field( 'post_excerpt', $attachment_id ) ) );

			/* Save the attachment as the 'featured image'. */
			if ( true === $args['thumbnail_id_save'] )
				set_post_thumbnail( $args['post_id'], $attachment_id );

			/* Return the image URL. */
			return array( 'src' => $image[0], 'url' => $image[0], 'alt' => $alt );
		}

		/* Return false for anything else. */
		return false;
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
		if ( isset( $matches ) && !empty( $matches[1][0] ) )
			return array( 'src' => $matches[1][0], 'url' => $matches[1][0] );

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

		/* If there is no image URL, return false. */
		if ( empty( $image['src'] ) )
			return false;

		/* Extract the arguments for easy-to-use variables. */
		extract( $args );

		/* If there is alt text, set it.  Otherwise, default to the post title. */
		$image_alt = ( ( !empty( $image['alt'] ) ) ? $image['alt'] : apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) ) );

		/* If there is a width or height, set them as HMTL-ready attributes. */
		$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
		$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );

		/* Loop through the custom field keys and add them as classes. */
		if ( is_array( $meta_key ) ) {
			foreach ( $meta_key as $key )
				$classes[] = sanitize_html_class( $key );
		}

		/* Add the $size and any user-added $image_class to the class. */
		$classes[] = sanitize_html_class( $size );
		$classes[] = sanitize_text_field( $image_class );

		/* Join all the classes into a single string and make sure there are no duplicates. */
		$class = join( ' ', array_unique( $classes ) );

		/* Add the image attributes to the <img /> element. */
		$html = '<img src="' . $image['src'] . '" alt="' . esc_attr( strip_tags( $image_alt ) ) . '" class="' . esc_attr( $class ) . '"' . $width . $height . ' />';

		/* If $link_to_post is set to true, link the image to its post. */
		if ( $link_to_post )
			$html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) ) ) . '">' . $html . '</a>';

		/* If there is a $post_thumbnail_id, apply the WP filters normally associated with get_the_post_thumbnail(). */
		if ( !empty( $image['post_thumbnail_id'] ) )
			$html = apply_filters( 'post_thumbnail_html', $html, $post_id, $image['post_thumbnail_id'], $size, '' );

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
