<?php
/**
 * Methods for handling front-end CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Public_Styles extends CareLib_Styles {
	/**
	 * The absolute path to the parent stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $parent;

	/**
	 * The absolute path to the child stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $child;

	/**
	 * The URI to the parent stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $parent_uri;

	/**
	 * The URI to the child stylesheet with a trailing slash.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $child_uri;

	/**
	 * A reference to the CareLib_Fonts class.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Fonts
	 */
	protected $fonts;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->parent     = trailingslashit( get_template_directory() );
		$this->child      = trailingslashit( get_stylesheet_directory() );
		$this->parent_uri = trailingslashit( get_template_directory_uri() );
		$this->child_uri  = trailingslashit( get_stylesheet_directory_uri() );
		parent::__construct();
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		add_action( 'wp_enqueue_scripts',    array( $this, 'register_styles' ),       0 );
		add_filter( 'stylesheet_uri',        array( $this, 'min_stylesheet_uri' ),    5, 2 );
		add_filter( 'stylesheet_uri',        array( $this, 'style_filter' ),         15 );
		add_filter( 'locale_stylesheet_uri', array( $this, 'locale_stylesheet_uri' ), 5 );
	}

	/**
	 * Add support for the CareLib Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function add_fonts_support() {
		$this->fonts = carelib_get( 'fonts-hooks' )->public_styles( $this );
	}

	/**
	 * Register front-end stylesheets for the library.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_styles() {
		wp_register_style(
			"{$this->prefix}-parent",
			$this->get_parent_stylesheet_uri(),
			array(),
			$this->theme_version()
		);
		wp_register_style(
			"{$this->prefix}-style",
			get_stylesheet_uri(),
			array(),
			$this->theme_version()
		);
	}

	/**
	 * Returns the parent theme stylesheet URI. Will return the active theme's
	 * stylesheet URI if no child theme is active. Be sure to check
	 * `is_child_theme()` when using.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_parent_stylesheet_uri() {
		// Get the parent theme stylesheet.
		$stylesheet_uri = $this->parent_uri . 'style.css';

		// If a '.min' version of the parent theme stylesheet exists, use it.
		if ( $this->suffix && file_exists( $this->parent . "style{$this->suffix}.css" ) ) {
			$stylesheet_uri = $this->parent_uri . "style{$this->suffix}.css";
		}

		return apply_filters( 'get_parent_stylesheet_uri', $stylesheet_uri );
	}

	/**
	 * Filter the 'stylesheet_uri' to load a minified version of 'style.css'
	 * file if it is available.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $stylesheet_uri The URI of the active theme's stylesheet.
	 * @param  string $stylesheet_dir_uri The directory URI of the active theme's stylesheet.
	 * @return string $stylesheet_uri
	 */
	public function min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
		if ( ! $this->suffix ) {
			return $stylesheet_uri;
		}

		// Remove the stylesheet directory URI from the file name.
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		// Change the stylesheet name to 'style.min.css'.
		$stylesheet = str_replace( '.css', "{$this->suffix}.css", $stylesheet );

		if ( file_exists( $this->child . $stylesheet ) ) {
			$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );
		}

		return $stylesheet_uri;
	}

	/**
	 * Retrieve the theme file with the highest priority that exists.
	 *
	 * @since  0.2.0
	 * @access public
	 * @link   http://core.trac.wordpress.org/ticket/18302
	 * @param  array  $file_names The files to search for.
	 * @return string
	 */
	protected function locate_theme_file( $file_names ) {
		$located = '';

		foreach ( (array) $file_names as $file ) {
			// If the file exists in the stylesheet (child theme) directory.
			if ( is_child_theme() && file_exists( $this->child . $file ) ) {
				$located = $this->child_uri . $file;
				break;
			}
			// If the file exists in the template (parent theme) directory.
			if ( file_exists( $this->parent . $file ) ) {
				$located = $this->parent_uri . $file;
				break;
			}
		}

		return $located;
	}

	/**
	 * Filters the 'stylesheet_uri' and checks if a post has a style that should
	 * overwrite the theme's primary `style.css`.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $stylesheet_uri
	 * @return string
	 */
	public function style_filter( $stylesheet_uri ) {
		if ( ! is_singular() ) {
			return $stylesheet_uri;
		}
		$style = $this->get_post_style( get_queried_object_id() );

		if ( $style && $style_uri = $this->locate_theme_file( array( $style ) ) ) {
			$stylesheet_uri = $style_uri;
		}

		return $stylesheet_uri;
	}

	/**
	 * Searches for a locale stylesheet. This function looks for stylesheets in
	 * the `css` folder in the following order:
	 *
	 * 1) $lang-$region.css,
	 * 2) $region.css,
	 * 3) $lang.css,
	 * 4) $text_direction.css.
	 *
	 * It first checks the child theme for these files. If they are not present,
	 * it will check the parent theme. This is much more robust than the
	 * WordPress locale stylesheet, allowing for multiple variations and a more
	 * flexible hierarchy.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_locale_style() {
		$i18n = carelib_get( 'i18n' );
		$styles = array();

		// Get the locale, language, and region.
		$locale = strtolower( str_replace( '_', '-', get_locale() ) );
		$lang   = strtolower( $i18n->get_language() );
		$region = strtolower( $i18n->get_region() );

		$styles[] = "css/{$locale}.css";

		if ( $region !== $locale ) {
			$styles[] = "css/{$region}.css";
		}

		if ( $lang !== $locale ) {
			$styles[] = "css/{$lang}.css";
		}

		$styles[] = is_rtl() ? 'css/rtl.css' : 'css/ltr.css';

		return $this->locate_theme_file( $styles );
	}

	/**
	 * Filters `locale_stylesheet_uri` with a more robust version for checking
	 * locale/language/region/direction stylesheets.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $stylesheet_uri
	 * @return string
	 */
	public function locale_stylesheet_uri( $stylesheet_uri ) {
		$locale_style = $this->get_locale_style();

		return $locale_style ? esc_url( $locale_style ) : $stylesheet_uri;
	}

	/**
	 * Enqueue fonts.
	 *
	 * @since 0.2.0
	 */
	public function enqueue_fonts_styles() {
		if ( $url = $this->fonts->get_google_fonts_url() ) {
			wp_enqueue_style( 'carelib-fonts-google', $url );
		}
	}

	/**
	 * Add embedded styles to render custom fonts for text groups.
	 *
	 * The Customizer JavaScript handles CSS, so short-circuit if the current
	 * request is a Customizer preview frame.
	 *
	 * @since 0.2.0
	 */
	public function add_inline_fonts_styles() {
		if ( is_customize_preview() ) {
			return;
		}
		if ( $css = $this->fonts->get_css() ) {
			wp_add_inline_style( "{$this->prefix}-style", $css );
		}
	}
}
