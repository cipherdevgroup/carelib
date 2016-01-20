<?php
/**
 * Build all the default classes necessary for the customizer features to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Customize_Setup_Factory extends CareLib_Factory {
	/**
	 * A list of required customize library object names.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected $required = array(
		'customize-setup-register',
		'customize-setup-scripts',
		'customize-setup-settings',
		'customize-setup-styles',
	);

	/**
	 * Method to fire all actions within the class.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'build_required_objects' ) );
	}
}
