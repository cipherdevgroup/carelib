<?php
/**
 * Customize control class to handle theme layouts.
 *
 * By default, it simply outputs a custom set of radio inputs. Theme authors
 * can extend this class and do something even cooler.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

class CareLib_Customize_Control_Layout extends CareLib_Customize_Control_Radio_Image {
	/**
	 * The default customizer section this control is attached to.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $section = 'layout';

	/**
	 * Placeholder for the CareLib_Layouts class.
	 *
	 * @since 0.2.0
	 * @var   object
	 */
	protected $layouts;

	/**
	 * Set up our control.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {
		// Array of allowed layouts. Pass via `$args['layouts']`.
		$allowed = ! empty( $args['layouts'] ) ? $args['layouts'] : array_keys( carelib_get_layouts() );

		// Loop through each of the layouts and add it to the choices array with proper key/value pairs.
		foreach ( carelib_get_layouts() as $layout ) {
			if ( in_array( $layout->get_name(), $allowed, true ) && ! ( 'theme_layout' === $id && false === $layout->is_global() ) && $layout->get_image() ) {

				$args['choices'][ $layout->get_name() ] = array(
					'label' => $layout->get_label(),
					'url'   => sprintf( $layout->get_image(), get_template_directory_uri(), get_stylesheet_directory_uri() ),
				);
			}
		}

		parent::__construct( $manager, $id, $args );
	}
}
