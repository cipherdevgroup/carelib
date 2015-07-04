<?php
/**
 * Load all required library files.
 *
 * @package    CareLib
 * @subpackage HybridCore
 * @copyright  Copyright (c) 2015, WP Site Care, LLC
 * @license    GPL-2.0+
 * @since      0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class for common theme functionality.
 *
 * @version 0.1.0
 */
class CareLib {

	/**
	 * Our library version number.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	protected $version = '0.1.0';

	/**
	 * Prefix to prevent conflicts.
	 *
	 * Used to prefix filters to make them unique.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	protected $prefix;

	/**
	 * The main library file.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $file = __FILE__;

	/**
	 * The library's directory path with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $dir;

	/**
	 * The library directory URL with a trailing slash.
	 *
	 * @since 0.1.0
	 * @var   string
	 */
	private $url;

	/**
	 * Constructor method.
	 *
	 * @since 0.1.0
	 * @param array $args arguments to be passed in via the helper function.
	 */
	public function __construct( $args = array() ) {
		$this->dir    = trailingslashit( dirname( __FILE__ ) );
		$this->uri    = trailingslashit( $this->normalize_uri( dirname( __FILE__ ) ) );
		$this->prefix = empty( $args['prefix'] ) ? 'carelib' : sanitize_key( $args['prefix'] );
	}

	/**
	 * Main CareLib Instance
	 *
	 * Insures that only one instance of CareLib exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 0.1.0
	 * @static
	 * @uses   CareLib::includes() Include the required files
	 * @return CareLib
	 */
	public static function instance( $args = array() ) {
		static $instance;
		if ( null === $instance ) {
			$instance = new self( $args );
		}
		return $instance;
	}

	/**
	 * Method to initialize the plugin.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function run() {
		spl_autoload_register( array( $this, 'autoloader' ) );
		self::includes();
		self::build( 'CareLib_Factory' );
		self::build_extensions( 'CareLib_Factory' );
	}

	/**
	 * Whether the current request is a Customizer preview.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  bool
	 */
	public function is_customizer_preview() {
		global $wp_customize;
		return $wp_customize instanceof WP_Customize_Manager && $wp_customize->is_preview();
	}

	/**
	 * Whether the current environment is WordPress.com.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  bool
	 */
	public function is_wpcom() {
		return apply_filters( 'carelib_is_wpcom', false );
	}

	/**
	 * Getter method for reading the protected prefix variable.
	 *
	 * @since   0.2.0
	 * @access  public
	 * @return  bool
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Return the path to the CareLib directory with a trailing slash.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  string
	 */
	public function get_dir() {
		return $this->dir;
	}

	/**
	 * Return the URI to the CareLib directory with a trailing slash.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_uri() {
		return $this->uri;
	}

	/**
	 * Fix asset directory path on Windows installations.
	 *
	 * Because we don't know where the library is located, we need to
	 * generate a URI based on the library directory path. In order to do
	 * this, we are replacing the theme root directory portion of the
	 * library directory with the theme root URI.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @uses   get_theme_root()
	 * @uses   get_theme_root_uri()
	 * @return void
	 */
	protected function normalize_uri( $path ) {
		return str_replace(
			str_replace( '\\', '/', get_theme_root() ),
			get_theme_root_uri(),
			str_replace( '\\', '/', $path )
		);
	}

	/**
	 * Load all plugin classes when they're instantiated.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function autoloader( $class ) {
		$class = strtolower( str_replace( '_', '-', str_replace( __CLASS__ . '_', '', $class ) ) );
		$file  = "{$this->dir}inc/class-{$class}.php";

		if ( false !== strpos( $class, 'admin' ) ) {
			$class = str_replace( 'admin-', '', $class );
			$file  = "{$this->dir}admin/class-{$class}.php";
		}

		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}
		return false;
	}

	/**
	 * Load all library functions.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function includes() {
		if ( ! is_admin() ) {
			require_once "{$this->dir}inc/tha-hooks.php";
		}
	}

	/**
	 * Store a reference to our classes and get them running.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	protected function build( $factory ) {
		$runnable_classes = array(
			'breadcrumb-display',
		);
		if ( is_admin() ) {
			$runnable_classes[] = 'admin-author-box';
			$runnable_classes[] = 'admin-dashboard';
			$runnable_classes[] = 'admin-tinymce';
		} else {
			$factory::build( 'style-builder' );
			$factory::build( 'template-tags' );
			$runnable_classes[] = 'attributes';
			$runnable_classes[] = 'author-box';
			$runnable_classes[] = 'search-form';
			$runnable_classes[] = 'seo';
		}

		foreach ( $runnable_classes as $class ) {
			$factory::build( $class );
			$factory::get( $class )->run();
		}
	}

	/**
	 * Store a reference to our optional classes and get them running.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  $factory string the name of our factory class
	 * @return void
	 */
	protected function build_extensions( $factory ) {
		$prefix  = $this->prefix;
		$runnable_classes = array();

		if ( current_theme_supports( "{$prefix}-footer-widgets" ) && ! is_admin() ) {
			$runnable_classes[] = 'footer-widgets';
		}

		if ( current_theme_supports( 'site-logo' ) && ! function_exists( 'jetpack_the_site_logo' ) ) {
			$runnable_classes[] = 'site-logo';
		}

		foreach ( $runnable_classes as $class ) {
			$factory::build( $class );
			$factory::get( $class )->run();
		}
	}

}

/**
 * Grab an instance of the main library class. If you need to reference a
 * method in the class for some reason, do it using this function.
 *
 * Example:
 *
 * <?php carelib()->is_customizer_preview(); ?>
 *
 * @since   0.1.0
 * @return  object CareLib
 */
function carelib( $args = array() ) {
	return CareLib::instance( $args );
}
