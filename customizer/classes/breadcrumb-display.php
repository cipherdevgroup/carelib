<?php
/**
 * Options for displaying breadcrumbs for use in the WordPress customizer.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * Our Breadcrumb display class for managing breadcrumbs through the Customizer.
 *
 * @package SiteCareLibrary
 */
class SiteCare_Breadcrumb_Display extends SiteCare_Customizer_Base {

	protected $section = 'sitecare_breadcrumbs';

	/**
	 * Register our customizer breadcrumb options for the parent class to load.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  object  $wp_customize
	 * @return void
	 */
	public function register( $wp_customize ) {

		$wp_customize->add_section(
			$this->section,
			array(
				'title'       => __( 'Breadcrumbs', 'sitecare-library' ),
				'description' => __( 'Choose where you would like breadcrumbs to display.', 'sitecare-library' ),
				'priority'    => 110,
				'capability'  => $this->capability,
			)
		);

		$priority = 10;

		foreach ( $this->get_options() as $breadcrumb => $setting ) {

			$wp_customize->add_setting(
				$breadcrumb,
				array(
					'default'           => $setting['default'],
					'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				$breadcrumb,
				array(
					'label'    => $setting['label'],
					'section'  => $this->section,
					'type'     => 'checkbox',
					'priority' => $priority++,
				)
			);
		}
	}

	/**
	 * An array of breadcrumb locations.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return array $breadcrumbs
	 */
	public function get_options() {
		return apply_filters( 'sitecare_breadcrumb_options', array(
			'sitecare_breadcrumb_single' => array(
				'default'  => 0,
				'label'    => __( 'Single Entries', 'sitecare-library' ),
			),
			'sitecare_breadcrumb_pages' => array(
				'default'  => 0,
				'label'    => __( 'Pages', 'sitecare-library' ),
			),
			'sitecare_breadcrumb_blog_page' => array(
				'default'  => 0,
				'label'    => __( 'Blog Page', 'sitecare-library' ),
			),
			'sitecare_breadcrumb_archive' => array(
				'default'  => 0,
				'label'    => __( 'Archives', 'sitecare-library' ),
			),
			'sitecare_breadcrumb_404' => array(
				'default'  => 0,
				'label'    => __( '404 Page', 'sitecare-library' ),
			),
			'sitecare_breadcrumb_attachment' => array(
				'default'  => 0,
				'label'    => __( 'Attachment/Media Pages', 'sitecare-library' ),
			),
		) );
	}

	/**
	 * Deprecated wrapper method for backwards compatbility.
	 *
	 * @deprecated use get_options instead.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return array $breadcrumbs
	 */
	public function get_breadcrumb_options() {
		return $this->get_options();
	}

}
