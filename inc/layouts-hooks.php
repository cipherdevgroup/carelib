<?php
/**
 * Methods for interacting with `CareLib_Layout` objects.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Layouts_Hooks extends CareLib_Layouts {
	/**
	 * Add customize register actions and filters for the CareLib_Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object CareLib_Fonts_Hooks
	 */
	public function customize_register( CareLib_Customize_Setup_Register $class ) {
		add_action( 'customize_register', array( $class, 'customize_register_layouts' ) );

		return $this;
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	public function load_metabox( CareLib_Admin_Metabox_Post_Layouts $class ) {
		add_action( 'load-post.php',     array( $class, 'setup_metabox' ) );
		add_action( 'load-post-new.php', array( $class, 'setup_metabox' ) );

		return $this;
	}
}
