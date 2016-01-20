<?php
/**
 * Build all the default classes necessary for the library to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Admin_Factory extends CareLib_Factory {
	/**
	 * A list of required admin library object names.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected $required = array(
		'admin-metabox-post-styles',
		'admin-metabox-post-templates',
		'admin-scripts',
		'admin-styles',
	);

	/**
	 * Method to fire all actions within the class.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'build_required_objects' ), -90 );
	}
}
