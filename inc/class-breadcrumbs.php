<?php
/**
 * Methods for controlling breadcrumb display.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Creates options for displaying breadcrumbs if they exist.
 *
 * @since 0.6.0
 */
class CareLib_Breadcrumbs {

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
	 * An array of breadcrumb locations.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return array $breadcrumbs
	 */
	protected function get_options() {
		$prefix = $this->prefix;
		return apply_filters( "{$prefix}_breadcrumb_options", array(
			"{$prefix}_breadcrumb_single" => array(
				'default'  => 0,
				'label'    => __( 'Single Entries', 'carelib' ),
			),
			"{$prefix}_breadcrumb_pages" => array(
				'default'  => 0,
				'label'    => __( 'Pages', 'carelib' ),
			),
			"{$prefix}_breadcrumb_blog_page" => array(
				'default'  => 0,
				'label'    => __( 'Blog Page', 'carelib' ),
			),
			"{$prefix}_breadcrumb_archive" => array(
				'default'  => 0,
				'label'    => __( 'Archives', 'carelib' ),
			),
			"{$prefix}_breadcrumb_404" => array(
				'default'  => 0,
				'label'    => __( '404 Page', 'carelib' ),
			),
			"{$prefix}_breadcrumb_attachment" => array(
				'default'  => 0,
				'label'    => __( 'Attachment/Media Pages', 'carelib' ),
			),
		) );
	}

	/**
	 * Display our breadcrumbs based on selections made in the WordPress customizer.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return bool true if both our template tag and theme mod return true.
	 */
	public function display() {
		// Grab our available breadcrumb display options.
		$options = array_keys( $this->get_options() );
		// Set up an array of template tags to map to our breadcrumb display options.
		$tags = apply_filters( "{$this->prefix}_breadcrumb_tags",
			array(
				is_singular() && ! is_attachment() && ! is_page(),
				is_page(),
				is_home() && ! is_front_page(),
				is_archive(),
				is_404(),
				is_attachment(),
			)
		);

		// Loop through our theme mods to see if we have a match.
		foreach ( array_combine( $options, $tags ) as $mod => $tag ) {
			// Return true if we find an enabled theme mod within the correct section.
			if ( 1 === absint( get_theme_mod( $mod, 0 ) ) && true === $tag ) {
				return true;
			}
		}
		return false;
	}

}
