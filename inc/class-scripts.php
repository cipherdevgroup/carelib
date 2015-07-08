<?php
/**
 * Methods for handling JavaScript and CSS in the framework.
 *
 * Themes can add support for the 'hybrid-core-scripts' feature to allow the
 * framework to handle loading the stylesheets into the theme header or footer
 * at an appropriate time.
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
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $parent;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $child;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $parent_uri;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $child_uri;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix     = CareLib::instance()->get_prefix();
		$this->suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->parent     = trailingslashit( get_template_directory() );
		$this->child      = trailingslashit( get_stylesheet_directory() );
		$this->parent_uri = trailingslashit( get_template_directory_uri() );
		$this->child_uri  = trailingslashit( get_stylesheet_directory_uri() );
	}

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	protected function wp_hooks() {
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_scripts' ),       5 );
		add_action( 'wp_enqueue_scripts',    array( $this, 'register_styles' ),       0 );
		add_filter( 'stylesheet_uri',        array( $this, 'min_stylesheet_uri' ),    5, 2 );
		add_filter( 'stylesheet_uri',        array( $this, 'style_filter' ),         15 );
		add_filter( 'locale_stylesheet_uri', array( $this, 'locale_stylesheet_uri' ), 5 );
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
	 * Tells WordPress to load the scripts needed for the framework using the
	 * wp_enqueue_script() function.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {
		// Load the comment reply script on singular posts with open comments if threaded comments are supported.
		if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Registers stylesheets for the framework. This function merely registers styles with WordPress using
	 * the wp_register_style() function. It does not load any stylesheets on the site. If a theme wants to
	 * register its own custom styles, it should do so on the 'wp_enqueue_scripts' hook.
	 *
	 * @since  1.5.0
	 * @access public
	 * @return void
	 */
	public function register_styles() {
		wp_register_style( "{$this->prefix}-parent", $this->get_parent_stylesheet_uri() );
		wp_register_style( "{$this->prefix}-style",  get_stylesheet_uri() );
	}

	/**
	 * Returns the parent theme stylesheet URI. Will return the active theme's stylesheet URI if no child
	 * theme is active. Be sure to check `is_child_theme()` when using.
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

		return apply_filters( '$this->get_parent_stylesheet_uri', $stylesheet_uri );
	}

	/**
	 * Filters the 'stylesheet_uri' to allow theme developers to offer a minimized version of their main
	 * 'style.css' file. It will detect if a 'style.min.css' file is available and use it if SCRIPT_DEBUG
	 * is disabled.
	 *
	 * @since  1.5.0
	 * @access public
	 * @param  string  $stylesheet_uri      The URI of the active theme's stylesheet.
	 * @param  string  $stylesheet_dir_uri  The directory URI of the active theme's stylesheet.
	 * @return string  $stylesheet_uri
	 */
	public function min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
		// Use the .min stylesheet if available.
		if ( $this->suffix ) {

			// Remove the stylesheet directory URI from the file name.
			$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

			// Change the stylesheet name to 'style.min.css'.
			$stylesheet = str_replace( '.css', "{$this->suffix}.css", $stylesheet );

			// If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet.
			if ( file_exists( $this->child . $stylesheet ) ) {
				$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );
			}
		}

		// Return the theme stylesheet.
		return $stylesheet_uri;
	}

	/**
	 * Retrieves the file with the highest priority that exists. The function searches both the stylesheet
	 * and template directories. This function is similar to the locate_template() function in WordPress
	 * but returns the file name with the URI path instead of the directory path.
	 *
	 * @since  1.5.0
	 * @access public
	 * @link   http://core.trac.wordpress.org/ticket/18302
	 * @param  array  $file_names The files to search for.
	 * @return string
	 */
	protected function locate_theme_file( $file_names ) {
		$located = '';

		// Loops through each of the given file names.
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
	 * Filters `locale_stylesheet_uri` with a more robust version for checking locale/language/region/direction
	 * stylesheets.
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
	 * Searches for a locale stylesheet. This function looks for stylesheets in the `css` folder in the following
	 * order:  1) $lang-$region.css, 2) $region.css, 3) $lang.css, and 4) $text_direction.css. It first checks
	 * the child theme for these files. If they are not present, it will check the parent theme. This is much
	 * more robust than the WordPress locale stylesheet, allowing for multiple variations and a more flexible
	 * hierarchy.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return string
	 */
	public function get_locale_style() {
		$i18n = CareLib_Factory::get( 'i18n' );
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
	 * Filters the 'stylesheet_uri' and checks if a post has a style that should overwrite the theme's
	 * primary `style.css`.
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
