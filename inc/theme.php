<?php
/**
 * Getters for the parent and child theme objects which will only spin up a new
 * instance of WP_Theme a single time.
 *
 * @package    CareLib
 * @subpackage CareLib\Classes
 * @author     Robert Neu
 * @copyright  Copyright (c) 2016, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.2.0
 */

defined( 'ABSPATH' ) || exit;

class CareLib_Theme {
	/**
	 * The theme object.
	 *
	 * @since 0.2.0
	 * @var   WP_Theme
	 */
	protected static $theme;

	/**
	 * The parent theme object.
	 *
	 * @since 0.2.0
	 * @var   WP_Theme
	 */
	protected static $parent;

	/**
	 * Return a single instance of the current theme's WP_Theme object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return WP_Theme A single instance of the WP_Theme object.
	 */
	public function get() {
		if ( null === self::$theme ) {
			self::$theme = wp_get_theme();
		}

		return self::$theme;
	}

	/**
	 * Return a single instance of the parent theme's WP_Theme object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return WP_Theme A single instance of the parent's WP_Theme object.
	 */
	public function get_parent() {
		if ( null === self::$parent ) {
			self::$parent = wp_get_theme( get_template() );
		}

		return self::$parent;
	}

	/**
	 * Return the fallback version by getting it from the WP_Theme object.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string The current theme's version number.
	 */
	public function get_fallback_version() {
		return $this->get()->get( 'Version' );
	}

	/**
	 * Return the version number of the current parent theme.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string The current parent theme's version number.
	 */
	public function get_parent_version() {
		return defined( 'PARENT_THEME_VERSION' ) ? PARENT_THEME_VERSION : $this->get_fallback_version();
	}

	/**
	 * Return the version number of the current theme.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string The current theme's version number.
	 */
	public function get_version() {
		return defined( 'CHILD_THEME_VERSION' ) ? CHILD_THEME_VERSION : $this->get_parent_version();
	}
}
