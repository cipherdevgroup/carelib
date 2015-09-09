<?php
/**
 * Layout class for creating new layout objects.
 *
 * Layout registration is handled via the `CareLib_Layouts` class in
 * `class-layouts.php`.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Layout {

	/**
	 * Arguments for creating the layout object.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @var    array
	 */
	protected $args = array();

	/**
	 * Register a new layout object
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type bool    $is_global_layout
	 *     @type bool    $is_post_layout
	 *     @type bool    $is_user_layout
	 *     @type string  $label
	 *     @type string  $image
	 *     @type bool    $_builtin
	 *     @type bool    $_internal
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {
		$name = sanitize_key( $name );

		$this->args = wp_parse_args( $args, array(
			'label'            => $name,
			'image'            => '',
			'is_global_layout' => true,
			'is_post_layout'   => true,
			'is_user_layout'   => true,
			'_builtin'         => false,
			'_internal'        => false,
			'post_types'       => array(),
		) );

		$this->args['name'] = $name;

		if ( ! empty( $this->post_types ) ) {
			$this->add_post_type_support();
		}
	}

	/**
	 * Magic method to return the layout name if someone tries to output the
	 * layout object as a string.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Adds post type support for `theme-layouts` in the event that the layout has been
	 * explicitly set for one or more post types.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function add_post_type_support() {
		foreach ( (array) $this->post_types as $post_type ) {
			if ( ! post_type_supports( $post_type, 'theme-layouts' ) ) {
				add_post_type_support( $post_type, 'theme-layouts' );
			}
		}
	}

	/**
	 * Return the layout name.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string|bool false if no name has been set.
	 */
	public function get_name() {
		return isset( $this->args['name'] ) ? (string) $this->args['name'] : false;
	}

	/**
	 * Set the layout name.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $value
	 * @return string
	 */
	public function set_name( $value ) {
		$this->args['name'] = (string) $value;
	}

	/**
	 * Return the layout label.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string|bool false if no label has been set.
	 */
	public function get_label() {
		return isset( $this->args['label'] ) ? (string) $this->args['label'] : false;
	}

	/**
	 * Set the layout label.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $value
	 * @return string
	 */
	public function set_label( $value ) {
		$this->args['label'] = (string) $value;
	}

	/**
	 * Return the layout image.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string|bool false if no image has been set.
	 */
	public function get_image() {
		return isset( $this->args['image'] ) ? (string) $this->args['image'] : false;
	}

	/**
	 * Set the layout image.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $value
	 * @return string
	 */
	public function set_image( $value ) {
		$this->args['image'] = (string) $value;
	}

	/**
	 * Check whether or not the current layout is a global layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_global() {
		return isset( $this->args['is_global_layout'] ) ? (bool) $this->args['is_global_layout'] : false;
	}

	/**
	 * Set the global layout property for the current layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  bool $value
	 * @return bool
	 */
	public function set_is_global( $value ) {
		$this->args['is_global_layout'] = (bool) $value;
	}

	/**
	 * Check whether or not the current layout is a post layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_post() {
		return isset( $this->args['is_post_layout'] ) ? (bool) $this->args['is_post_layout'] : false;
	}

	/**
	 * Set the post layout property for the current layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  bool $value
	 * @return bool
	 */
	public function set_is_post( $value ) {
		$this->args['is_post_layout'] = (bool) $value;
	}

	/**
	 * Check whether or not the current layout is a user layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_user() {
		return isset( $this->args['is_user_layout'] ) ? (bool) $this->args['is_user_layout'] : false;
	}

	/**
	 * Set the user layout property for the current layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  bool $value
	 * @return bool
	 */
	public function set_is_user( $value ) {
		$this->args['is_user_layout'] = (bool) $value;
	}

	/**
	 * Check whether or not the current layout is a built-in layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_bultin() {
		return isset( $this->args['_builtin'] ) ? (bool) $this->args['_builtin'] : false;
	}

	/**
	 * Check whether or not the current layout is an internal layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return bool
	 */
	public function is_internal() {
		return isset( $this->args['_internal'] ) ? (bool) $this->args['_internal'] : false;
	}

	/**
	 * Get the supported post types property for the current layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return array
	 */
	public function get_post_types() {
		return isset( $this->args['post_types'] ) ? (array) $this->args['post_types'] : array();
	}

	/**
	 * Set the supported post types property for the current layout.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $value
	 * @return array
	 */
	public function set_post_types( $value ) {
		$this->args['post_types'] = (array) $value;
	}

}
