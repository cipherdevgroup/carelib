<?php
/**
 * Modifications to TinyMCE, the default WordPress editor.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * A class to customize the default styleselect options in the WP TinyMCE.
 *
 * @package CareLib
 */
class CareLib_TinyMCE_Admin {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Author_Box::$wp_hooks
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	private function wp_hooks() {
		add_filter( 'mce_buttons',          array( $this, 'add_styleselect' ),     99 );
		add_filter( 'mce_buttons_2',        array( $this, 'disable_styleselect' ), 99 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_formats' ),     99 );
	}

	/**
	 * Add styleselect button to the end of the first row of TinyMCE buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function add_styleselect( $buttons ) {
		// Get rid of styleselect if it's been added somewhere else.
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		array_push( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Remove styleselect button if it's been added to the second row of TinyMCE
	 * buttons.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $buttons array existing TinyMCE buttons
	 * @return $buttons array modified TinyMCE buttons
	 */
	public function disable_styleselect( $buttons ) {
		if ( in_array( 'styleselect', $buttons ) ) {
			unset( $buttons['styleselect'] );
		}
		return $buttons;
	}

	/**
	 * Add our custom CareLib styles to the styleselect dropdown button.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $args array existing TinyMCE arguments
	 * @return $args array modified TinyMCE arguments
	 * @see    http://wordpress.stackexchange.com/a/128950/9844
	 */
	public function tinymce_formats( $args ) {
		$formats = apply_filters( carelib()->get_prefix() . '_tiny_mce_formats',
			array(
				array(
					'title'    => __( 'Drop Cap', 'carelib' ),
					'inline'   => 'span',
					'classes'  => 'dropcap',
				),
				array(
					'title'    => __( 'Pull Quote Left', 'carelib' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignleft',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Pull Quote Right', 'carelib' ),
					'block'    => 'blockquote',
					'classes'  => 'pullquote alignright',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Intro Paragraph', 'carelib' ),
					'selector' => 'p',
					'classes'  => 'intro-pagragraph',
					'wrapper'  => true,
				),
				array(
					'title'    => __( 'Call to Action', 'carelib' ),
					'block'    => 'div',
					'classes'  => 'call-to-action',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Feature Box', 'carelib' ),
					'block'    => 'div',
					'classes'  => 'feature-box',
					'wrapper'  => true,
					'exact'    => true,
				),
				array(
					'title'    => __( 'Code Block', 'carelib' ),
					'format'   => 'pre',
				),
				array(
					'title'    => __( 'Buttons', 'carelib' ),
					'items'    => array(
						array(
							'title'    => __( 'Standard', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Standard Block', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button block',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta',
							'exact'    => true,
						),
						array(
							'title'    => __( 'Call to Action Block', 'carelib' ),
							'selector' => 'a',
							'classes'  => 'button secondary cta block',
							'exact'    => true,
						),
					),
				),
			)
		);
		// Merge with any existing formats which have been added by plugins.
		if ( ! empty( $args['style_formats'] ) ) {
			$existing_formats = json_decode( $args['style_formats'] );
			$formats = array_merge( $formats, $existing_formats );
		}

		$args['style_formats'] = wp_json_encode( $formats );

		return $args;
	}
}
