<?php
/**
 * Abstract SiteCare Customizer Base Class
 *
 * Rather than writing basic sanitization and registration methods every time we
 * want to hook into the WordPress customizer, we should try to reuse code as
 * much as possible. This abstract class allows us to add and sanitize any type
 * of customizer setting we like and reference our existing methods within the
 * child class.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * SiteCare_Customizer_Base
 *
 * An abstract class to provide basic helper methods to use when registering new
 * customizer sections within a theme.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
abstract class SiteCare_Customizer_Base {

	/**
	 * An array of choices used for sanitizing multi-select fields.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	public $choices = array();

	/**
	 * An array of defaults used for sanitizing multi-select fields.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	public $defaults = array();

	/**
	 * A default capability required for customizer options.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	protected $capability = 'edit_theme_options';

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   SiteCare_Customizer_Base::$customizer_hooks
	 * @return void
	 */
	public function run() {
		self::customizer_hooks();
	}

	/**
	 * Define defaults, call the `register` method, add css to head.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	protected function customizer_hooks() {
		// Throw a warning if no register method exists in the child class.
		if ( ! method_exists( $this, 'register' ) ) {
			_doing_it_wrong(
				'SiteCare_Customizer_Base',
				__( 'When extending SiteCare_Customizer_Base, you must create a register method.', 'sitecare-library' )
			);
		}
		// Register our customizer sections.
		add_action( 'customize_register', array( $this, 'register' ), 15 );

		// Register customizer scripts if the child class has added any.
		if ( method_exists( $this, 'scripts' ) ) {
			add_action( 'customize_preview_init', array( $this, 'scripts' ) );
		}
	}

	/**
	 * Sanitize a string to allow only tags in the allowedtags array.
	 *
	 * @since  0.1.0
	 * @param  string $string The unsanitized string.
	 * @return string The sanitized string.
	 */
	public function sanitize_text( $string ) {
		global $allowedtags;
		return wp_kses( $string , $allowedtags );
	}

	/**
	 * Sanitize a checkbox to only allow 0 or 1
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $input
	 * @return int
	 */
	public function sanitize_checkbox( $input ) {
		return ( 1 === absint( $input ) ) ? 1 : 0;
	}

	/**
	 * Sanitize the url of uploaded media.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $value The url to sanitize
	 * @return string $output The sanitized url.
	 */
	public function sanitize_file_url( $url ) {
		$output = '';

		$filetype = wp_check_filetype( $url );
		if ( $filetype['ext'] ) {
			$output = esc_url( $url );
		}

		return $output;
	}

	/**
	 * Sanitizes a hex color.
	 *
	 * Returns either '', a 3 or 6 digit hex color (with #), or null.
	 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $color
	 * @return string|null
	 */
	public function sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return null;
	}

	/**
	 * Sanitize a value from a list of allowed values.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  mixed $value The value to sanitize.
	 * @param  mixed $setting The setting for which the sanitizing is occurring.
	 * @return mixed The sanitized value.
	 */
	public function sanitize_choices( $value, $setting ) {
		if ( is_object( $setting ) ) {
			$setting = $setting->id;
		}
		if ( ! in_array( $value, array_keys( $this->get_choices( $setting ) ) ) ) {
			$value = $this->get_default( $setting );
		}
		return $value;
	}

	/**
	 * Helper function to return defaults as a string.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string
	 * @return string $default
	 */
	public function get_default( $setting ) {
		$default = '';
		if ( isset( $this->defaults[ $setting ] ) ) {
			$default = $this->defaults[ $setting ];
		}
		return $default;
	}

	/**
	 * Helper function to return choices as an array.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string
	 * @return array $default
	 */
	public function get_choices( $setting ) {
		$choices = array();
		if ( isset( $this->choices[ $setting ] ) ) {
			$choices = (array) $this->choices[ $setting ];
		}
		return $choices;
	}

}
