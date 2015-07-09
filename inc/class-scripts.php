<?php
/**
 * Methods for handling JavaScript and CSS in the framework.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * CareLib Translations class.
 */
class CareLib_Scripts {

	/**
	 * The library object.
	 *
	 * @since 0.2.0
	 * @type CareLib
	 */
	protected $lib;

	/**
	 * Library version number to append to scripts.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $version;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Script suffix to determine whether or not to load minified scripts.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $suffix;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->lib     = CareLib::instance();
		$this->version = $this->lib->get_version();
		$this->prefix  = $this->lib->get_prefix();
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * Helper function for getting the script/style `.min` suffix for minified files.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_suffix() {
		return $this->suffix;
	}

	/**
	 * Output the path to the library css directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function css_uri( $path ) {
		$this->lib->get_uri( 'css/' ) . $path;
	}

	/**
	 * Output the path to the library JavaScript directory with a trailing slash.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function js_uri( $path ) {
		$this->lib->get_uri( 'js/' ) . $path;
	}

	/**
	 * Gets a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function get_post_style( $post_id ) {
		return get_post_meta( $post_id, $this->get_style_meta_key(), true );
	}

	/**
	 * Sets a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @param  string  $layout
	 * @return bool
	 */
	public function set_post_style( $post_id, $style ) {
		return update_post_meta( $post_id, $this->get_style_meta_key(), $style );
	}

	/**
	 * Deletes a post style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function delete_post_style( $post_id ) {
		return delete_post_meta( $post_id, $this->get_style_meta_key() );
	}

	/**
	 * Checks a post if it has a specific style.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @return bool
	 */
	public function has_post_style( $style, $post_id = '' ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		return $this->get_post_style( $post_id ) === $style ? true : false;
	}

	/**
	 * Wrapper function for returning the metadata key used for objects that can use styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_style_meta_key() {
		return apply_filters( "{$this->prefix}_style_meta_key", 'Stylesheet' );
	}

}
