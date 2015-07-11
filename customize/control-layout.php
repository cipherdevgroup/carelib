<?php
/**
 * Customize control class to handle theme layouts.
 *
 * By default, it simply outputs a custom set of radio inputs. Theme authors
 * can extend this class and do something even cooler.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Theme Layout customize control class.
 *
 * @since  0.2.0
 * @access public
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
		$this->layouts = carelib_class( 'layouts' );

		// Array of allowed layouts. Pass via `$args['layouts']`.
		$allowed = ! empty( $args['layouts'] ) ? $args['layouts'] : array_keys( $this->layouts->get_layouts() );

		// Loop through each of the layouts and add it to the choices array with proper key/value pairs.
		foreach ( $this->layouts->get_layouts() as $layout ) {

			if ( in_array( $layout->name, $allowed ) && ! ( 'theme_layout' === $id && false === $layout->is_global_layout ) && $layout->image ) {

				$args['choices'][ $layout->name ] = array(
					'label' => $layout->label,
					'url'   => sprintf( $layout->image, get_template_directory_uri(), get_stylesheet_directory_uri() ),
				);
			}
		}

		// Let the parent class handle the rest.
		parent::__construct( $manager, $id, $args );
	}
}
