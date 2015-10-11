<?php
/**
 * Build all the default front-end classes necessary for the library to run.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Public_Factory extends CareLib_Factory {
	/**
	 * A list of required public library object names.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected $required = array(
		'attributes',
		'context',
		'filters',
		'head',
		'meta',
		'public-scripts',
		'public-styles',
		'search-form',
		'template-hierarchy',
	);

	/**
	 * Method to fire all actions within the class.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		if ( ! is_admin() ) {
			add_action( 'after_setup_theme', array( $this, 'build_required_objects' ), -95 );
		}
	}
}
