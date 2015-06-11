<?php
/**
 * The main CareLib Site Logo class.
 *
 * Based on the Jetpack site logo feature.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * Our Site Logo class for managing a theme-agnostic logo through the Customizer.
 *
 * @package CareLib
 */
class CareLib_Site_Logo extends CareLib_Customizer_Base {

	/**
	 * Stores our current logo settings.
	 */
	public $logo;

	/**
	 * Get our current logo settings stored in options.
	 *
	 * @uses get_option()
	 */
	public function __construct() {
		$this->logo = get_option( 'site_logo', null );
	}

	/**
	 * Return our instance, creating a new one if necessary.
	 *
	 * @return object CareLib_Site_Logo
	 */
	public function run() {
		parent::customizer_hooks();
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @uses CareLib_Site_Logo::head_text_styles()
	 * @uses CareLib_Site_Logo::body_classes()
	 * @uses CareLib_Site_Logo::media_manager_image_sizes()
	 * @uses add_action
	 * @uses add_filter
	 */
	protected function wp_hooks() {
		add_action( 'tha_body_top',            array( $this, 'head_text_styles' ) );
		add_action( 'delete_attachment',       array( $this, 'reset_on_attachment_delete' ) );
		add_filter( 'body_class',              array( $this, 'body_classes' ) );
		add_filter( 'image_size_names_choose', array( $this, 'media_manager_image_sizes' ) );
		add_filter( 'display_media_states',    array( $this, 'add_media_state' ) );
		add_filter( 'upload_mimes',            array( $this, 'add_svg_mime_type' ) );
	}

	/**
	 * Add our logo uploader to the Customizer.
	 *
	 * @param object $wp_customize Customizer object.
	 * @uses current_theme_supports()
	 * @uses current_theme_supports()
	 * @uses WP_Customize_Manager::add_setting()
	 * @uses WP_Customize_Manager::add_control()
	 * @uses CareLib_Site_Logo::sanitize_checkbox()
	 */
	public function register( $wp_customize ) {

		//Update the Customizer section title for discoverability.
		$wp_customize->get_section( 'title_tagline' )->title = __( 'Site Title, Tagline, and Logo', 'carelib' );

		// Disable the display header text control from the custom header feature.
		if ( current_theme_supports( 'custom-header' ) ) {
			$wp_customize->remove_control( 'display_header_text' );
		}

		// Add a setting to hide header text.
		$wp_customize->add_setting(
			'site_logo_header_text',
			array(
				'default'           => 1,
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'site_logo_header_text',
				array(
					'label'    => __( 'Display Header Text', 'carelib' ),
					'section'  => 'title_tagline',
					'settings' => 'site_logo_header_text',
					'type'     => 'checkbox',
				)
			)
		);

		// Add the setting for our logo value.
		$wp_customize->add_setting(
			'site_logo',
			array(
				'capability' => $this->capability,
				'default'    => array(
					'id'     => 0,
					'sizes'  => array(),
					'url'    => false,
				),
				'sanitize_callback' => array( $this, 'sanitize_logo_setting' ),
				'transport'         => 'postMessage',
				'type'              => 'option',
			)
		);

		// Add our image uploader.
		$wp_customize->add_control(
			new CareLib_Site_Logo_Image_Control(
				$wp_customize,
				'site_logo',
				array(
					'label'    => __( 'Logo', 'carelib' ),
					'section'  => 'title_tagline',
					'settings' => 'site_logo',
				)
			)
		);
	}

	/**
	 * Enqueue scripts for the Customizer live preview.
	 *
	 * @uses wp_enqueue_script()
	 * @uses plugins_url()
	 * @uses current_theme_supports()
	 * @uses CareLib_Site_Logo::header_text_classes()
	 * @uses wp_localize_script()
	 */
	public function scripts() {
		$assets_uri = carelib()->get_lib_uri() . 'assets/';

		wp_enqueue_script(
			'site-logo-preview',
			esc_url( $assets_uri ) . 'js/site-logo/preview.js',
			array( 'media-views' ),
			'',
			true
		);
		wp_enqueue_script(
			'site-logo-header-text',
			esc_url( $assets_uri ) . 'js/site-logo/header-text.js',
			array( 'media-views' ),
			'',
			true
		);
	}

	/**
	 * Hide header text on front-end if necessary.
	 *
	 * @uses current_theme_supports()
	 * @uses get_theme_mod()
	 * @uses CareLib_Site_Logo::header_text_classes()
	 * @uses esc_html()
	 */
	public function head_text_styles() {
		// Bail if our text isn't hidden.
		if ( get_theme_mod( 'site_logo_header_text', 1 ) ) {
			return;
		}
		// hide our header text if display Header Text is unchecked.
		add_filter( 'hybrid_attr_site-title',       array( $this, 'hide_text' ) );
		add_filter( 'hybrid_attr_site-description', array( $this, 'hide_text' ) );
	}

	/**
	 * Filter the attributes of our site title and description to hide them.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $attr array the current attributes
	 * @return $attr array the modified attributes
	 */
	public function hide_text( $attr ) {
		$attr['class'] = isset( $attr['class'] ) ? $attr['class'] .= ' screen-reader-text' : 'screen-reader-text';
		return $attr;
	}

	/**
	 * Reset the site logo if the current logo is deleted in the media manager.
	 *
	 * @param int $site_id
	 * @uses CareLib_Site_Logo::remove_site_logo()
	 */
	public function reset_on_attachment_delete( $post_id ) {
		// Do nothing if the logo id doesn't match the post id.
		if ( $this->logo['id'] !== $post_id ) {
			return;
		}
		$this->remove_site_logo();
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @uses CareLib_Site_Logo::has_site_logo()
	 * @return array Array of <body> classes
	 */
	public function body_classes( $classes ) {
		// Add a class if a Site Logo is active
		if ( $this->has_site_logo() ) {
			$classes[] = 'has-site-logo';
		}

		return $classes;
	}

	/**
	 * Make custom image sizes available to the media manager.
	 *
	 * @param  array $sizes
	 * @uses   get_intermediate_image_sizes()
	 * @return array All default and registered custom image sizes.
	 */
	public function media_manager_image_sizes( $sizes ) {
		// Get an array of all registered image sizes.
		$intermediate = get_intermediate_image_sizes();

		// Bail if we don't have any image sizes to work with.
		if ( empty( $intermediate ) ) {
			return;
		}
		foreach ( (array) $intermediate as $key => $size ) {
			// If the size isn't already in the $sizes array, add it.
			if ( ! array_key_exists( $size, $sizes ) ) {
				$sizes[ $size ] = $size;
			}
		}

		return $sizes;
	}

	/**
	 * Add site logos to media states in the Media Manager.
	 *
	 * @return array The current attachment's media states.
	 */
	public function add_media_state( $media_states ) {
		// Only bother testing if we have a site logo set.
		if ( ! $this->has_site_logo() ) {
			return $media_states;
		}
		global $post;

		// If our attachment ID and the site logo ID match, this image is the site logo.
		if ( $post->ID === $this->logo['id'] ) {
			$media_states[] = __( 'Site Logo', 'carelib' );
		}
		return $media_states;
	}

	/**
	 * Determine if a site logo is assigned or not.
	 *
	 * @since  0.1.0
	 * @uses   CareLib_Logo::$logo
	 * @return boolean True if there is an active logo, false otherwise
	 */
	public function has_site_logo() {
		return ( isset( $this->logo['id'] ) && 0 !== $this->logo['id'] ) ? true : false;
	}

	/**
	 * Reset the site logo option to zero (empty).
	 *
	 * @since  0.1.0
	 * @uses   update_option()
	 * @return void
	 */
	public function remove_site_logo() {
		update_option( 'site_logo',
			array(
				'id'    => 0,
				'sizes' => array(),
				'url'   => '',
			)
		);
	}

	/**
	 * Retrieve the site logo URL or ID (URL by default). Pass in the string 'id' for ID.
	 *
	 * @since  0.1.0
	 * @uses   get_option()
	 * @uses   esc_url_raw()
	 * @uses   set_url_scheme()
	 * @return mixed The URL or ID of our site logo, false if not set
	 */
	function get_site_logo( $format = 'url' ) {
		$logo = $this->logo;

		// Return false if no logo is set
		if ( ! isset( $logo['id'] ) || 0 === absint( $logo['id'] ) ) {
			return false;
		}

		// Return the ID if specified, otherwise return the URL by default
		if ( 'id' === $format ) {
			return $logo['id'];
		}

		return esc_url_raw( set_url_scheme( $logo['url'] ) );
	}

	/**
	 * Output an <img> tag of the site logo, at the size specified
	 * in the theme's add_theme_support() declaration.
	 *
	 * @since 0.1.0
	 * @uses CareLib_Logo::logo
	 * @uses CareLib_Logo::theme_size()
	 * @uses CareLib_Logo::has_site_logo()
	 * @uses CareLib::is_customizer_preview()
	 * @uses esc_url()
	 * @uses home_url()
	 * @uses esc_attr()
	 * @uses wp_get_attachment_image()
	 * @uses apply_filters()
	 */
	function the_site_logo() {
		$logo = $this->logo;
		$size = $this->theme_size();

		// Bail if no logo is set. Leave a placeholder if we're in the Customizer, though (needed for the live preview).
		if ( ! $this->has_site_logo() ) {
			if ( carelib()->is_customizer_preview() ) {
				printf( '<a href="%1$s" class="site-logo-link" style="display:none;"><img class="site-logo" data-size="%2$s" /></a>',
					esc_url( home_url( '/' ) ),
					esc_attr( $size )
				);
			}
			return;
		}

		// We have a logo. Logo is go.
		$html = sprintf( '<a href="%1$s" class="site-logo-link" rel="home">%2$s</a>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image(
				absint( $logo['id'] ),
				esc_attr( $size ),
				false,
				array(
					'class'     => 'site-logo attachment-' . esc_attr( $size ),
					'data-size' => esc_attr( $size ),
				)
			)
		);

		echo apply_filters( 'the_site_logo', $html, $logo, $size );
	}

	/**
	 * Determine image size to use for the logo.
	 *
	 * @uses get_theme_support()
	 * @return string Size specified in add_theme_support declaration, or 'thumbnail' default
	 */
	public function theme_size() {
		$args        = get_theme_support( 'site-logo' );
		$valid_sizes = get_intermediate_image_sizes();

		// Add 'full' to the list of accepted values.
		$valid_sizes[] = 'full';

		// If the size declared in add_theme_support is valid, use it; otherwise, just go with 'thumbnail'.
		$size = ( isset( $args[0]['size'] ) && in_array( $args[0]['size'], $valid_sizes ) ) ? $args[0]['size'] : 'thumbnail';

		return $size;
	}

	/**
	 * Validate and sanitize a new site logo setting.
	 *
	 * @param $input
	 * @return mixed 1 if checked, empty string if not checked.
	 */
	public function sanitize_logo_setting( $input ) {
		$input['id']  = absint( $input['id'] );
		$input['url'] = esc_url_raw( $input['url'] );

		// End here if we have an image to display.
		if ( wp_get_attachment_image_src( $input['id'] ) ) {
			return $input;
		}

		// If the new setting doesn't point to a valid attachment, reset it.
		return array( 'id' => 0, 'sizes' => array(), 'url' => '' );
	}

	/**
	 * Enable support for administrators to add SVG uploads.
	 *
	 * @param  $mimes array
	 * @return $mimes array modified mimes
	 */
	function add_svg_mime_type( $mimes ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $mimes;
		}
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

}
