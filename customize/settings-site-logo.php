<?php
/**
 * Customizer settings for the site logo.
 *
 * Based on the Jetpack site logo feature.
 *
 * @package    CareLib
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Our Site Logo class for managing a theme-agnostic logo through the Customizer.
 *
 * @package CareLib
 */
class CareLib_Settings_Site_Logo extends CareLib_Customizer_Base {

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
				'sanitize_callback' => 'absint',
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
		$wp_customize->add_control( CareLib_Factory::build( 'site-logo-control', '',
			array(
				$wp_customize,
				'site_logo',
				array(
					'label'    => __( 'Logo', 'carelib' ),
					'section'  => 'title_tagline',
					'settings' => 'site_logo',
				),
			)
		) );
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
		$uri = carelib()->get_uri();

		wp_enqueue_script(
			'site-logo-preview',
			esc_url( "{$uri}js/site-logo-preview.js" ),
			array( 'media-views' ),
			'',
			true
		);
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

}
