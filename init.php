<?php
/**
 * Load all required library files.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CareLib', false ) ) {

	/**
	 * Class for common CareLib theme functionality.
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
		 * Slashed directory path to load files.
		 *
		 * @since 0.1.0
		 * @type  string
		 */
		protected $dir;

		/**
		 * Placeholder for our style builder class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Style_Builder
		 */
		public $style_builder;

		/**
		 * Placeholder for our attributes class instance.
		 *
		 * @since 0.2.0
		 * @var   CareLib_Attributes
		 */
		public $attr;

		/**
		 * Placeholder for our author box class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Author_Box
		 */
		public $author_box;

		/**
		 * Placeholder for our breadcrumb display class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Breadcrumb_Display
		 */
		public $breadcrumb_display;

		/**
		 * Placeholder for our SEO class instance.
		 *
		 * @since 0.2.0
		 * @var   CareLib_SEO
		 */
		public $seo;

		/**
		 * Placeholder for our template tags class instance.
		 *
		 * @since 0.2.0
		 * @var   CareLib_Template_Tags
		 */
		public $tags;

		/**
		 * Placeholder for our footer widgets class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Footer_Widgets
		 */
		public $footer_widgets;

		/**
		 * Placeholder for our site logo class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Site_Logo
		 */
		public $site_logo;

		/**
		 * Placeholder for our author box admin class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib_Author_Box_Admin
		 */
		public $author_box_admin;

		/**
		 * Placeholder for our TinyMCE admin class instance.
		 *
		 * @since 0.2.0
		 * @var   CareLib_TinyMCE_Admin
		 */
		public $tinymce_admin;

		/**
		 * Static placeholder for our main class instance.
		 *
		 * @since 0.1.0
		 * @var   CareLib
		 */
		private static $instance;

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since  0.1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong(
				__FUNCTION__,
				esc_attr__( 'Cheatin&#8217; huh?', 'carelib' ),
				'0.1.0'
			);
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  0.1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong(
				__FUNCTION__,
				esc_attr__( 'Cheatin&#8217; huh?', 'carelib' ),
				'0.1.0'
			);
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
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CareLib ) ) {
				self::$instance = new CareLib;
				self::$instance->dir = trailingslashit( dirname( __FILE__ ) );
				self::$instance->prefix = empty( $args['prefix'] ) ? 'carelib' : sanitize_key( $args['prefix'] );
				self::$instance->includes();
				self::$instance->extensions_includes();
				self::$instance->instantiate();
				if ( is_admin() ) {
					self::$instance->admin_includes();
					self::$instance->admin_instantiate();
				}
			}
			return self::$instance;
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
		public function get_lib_dir() {
			return $this->dir;
		}

		/**
		 * Return the URI to the CareLib directory with a trailing slash.
		 *
		 * Because we don't know where the library is located, we need to
		 * generate a URI based on the library directory path. In order to do
		 * this, we are replacing the theme root directory portion of the
		 * library directory with the theme root URI.
		 *
		 * @since  0.1.0
		 * @access public
		 * @uses   get_theme_root()
		 * @uses   get_theme_root_uri()
		 * @uses   CareLib::normalize_path()
		 * @uses   CareLib::$dir
		 * @return string
		 */
		public function get_lib_uri() {
			$root = $this->normalize_path( get_theme_root() );
			$dir  = $this->normalize_path( $this->dir );
			return trailingslashit( str_replace( $root, get_theme_root_uri(), $dir ) );
		}

		/**
		 * Fix asset directory path on Windows installations.
		 *
		 * In order to get the absolute uri of our library directory without
		 * hard-coding it, we need to use some WordPress functions which return
		 * server paths. Unfortunately, on Windows these result in unexpected
		 * results due to a difference in the way paths are formatted.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function normalize_path( $path ) {
			return str_replace( '\\', '/', $path );
		}

		/**
		 * Include required library files.
		 *
		 * If for some reason you would prefer that a particular file isn't
		 * loaded you can use the carelib_includes filter to unset it
		 * before the includes runs.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			require_once $this->dir . 'customizer/classes/customizer-base.php';
			require_once $this->dir . 'classes/search-form.php';
			require_once $this->dir . 'classes/style-builder.php';
			require_once $this->dir . 'classes/attr.php';
			require_once $this->dir . 'classes/seo.php';
			require_once $this->dir . 'classes/template-tags.php';
			require_once $this->dir . 'functions/tha-hooks.php';
		}

		/**
		 * Include extensions init files only when theme support has been added.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function extensions_includes() {
			if ( current_theme_supports( 'carelib-author-box' ) ) {
				require_once $this->dir . 'classes/author-box.php';
			}
			if ( current_theme_supports( 'breadcrumb-trail' ) ) {
				require_once $this->dir . 'customizer/classes/breadcrumb-display.php';
			}
			if ( current_theme_supports( 'carelib-footer-widgets' ) ) {
				require_once $this->dir . 'classes/footer-widgets.php';
			}
			if ( current_theme_supports( 'site-logo' ) ) {
				add_action( 'init', array( $this, 'logo_includes' ), 12 );
			}
		}

		/**
		 * Activate the CareLib Logo plugin. We need to hook into init in order
		 * to check for the Jetpack/Automattic version of the logo uploader.
		 *
		 * @since  0.1.0
		 * @uses   current_theme_supports()
		 * @return void
		 */
		function logo_includes() {
			// Return early if the standalone plugin and/or Jetpack module is activated.
			if ( class_exists( 'Site_Logo', false ) ) {
				return;
			}
			require_once $this->dir . 'customizer/classes/site-logo.php';
			if ( ! $this->is_customizer_preview() ) {
				return;
			}
			require_once $this->dir . 'customizer/controls/site-logo.php';
		}

		/**
		 * Include admin library files.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function admin_includes() {
			require_once $this->dir . 'admin/classes/tiny-mce.php';
			if ( current_theme_supports( 'carelib-author-box' ) ) {
				require_once $this->dir . 'admin/classes/author-box.php';
			}
		}

		/**
		* Spin up instances of our front-end classes once they've been included.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function instantiate() {
			$this->style_builder = new CareLib_Style_Builder;
			$this->attr          = new CareLib_Attributes;
			$search_form         = new CareLib_Search_Form;
			$this->seo           = new CareLib_SEO;
			$this->tags          = new CareLib_Template_Tags;

			$this->attr->run();
			$search_form->run();
			$this->seo->run();

			if ( class_exists( 'CareLib_Author_Box', false ) ) {
				$this->author_box = new CareLib_Author_Box;
				$this->author_box->run();
			}
			if ( class_exists( 'CareLib_Breadcrumb_Display', false ) ) {
				$this->breadcrumb_display = new CareLib_Breadcrumb_Display;
				$this->breadcrumb_display->run();
			}
			if ( class_exists( 'CareLib_Footer_Widgets', false ) ) {
				$this->footer_widgets = new CareLib_Footer_Widgets;
				$this->footer_widgets->run();
			}

			add_action( 'init', array( $this, 'instantiate_logo' ), 13 );
		}

		/**
		 * Because the Automattic and Jetpack Site Logo feature is hooked into
		 * init, we need to hook in a little later to add our functionality. If
		 * one of the other plugins is detected we'll just return.
		 *
		 * @since  0.1.0
		 * @uses   CareLib_Site_Logo::run()
		 * @return object Site_Logo
		 */
		function instantiate_logo() {
			if ( ! class_exists( 'CareLib_Site_Logo', false ) ) {
				return;
			}
			$this->site_logo = new CareLib_Site_Logo;
			$this->site_logo->run();
		}

		/**
		 * Spin up instances of our admin classes once they've been included.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function admin_instantiate() {
			$this->tinymce_admin = new CareLib_TinyMCE_Admin;
			$this->tinymce_admin->run();

			if ( class_exists( 'CareLib_Author_Box_Admin', false ) ) {
				$this->author_box_admin = new CareLib_Author_Box_Admin;
				$this->author_box_admin->run();
			}
		}

	}
}

if ( ! function_exists( 'carelib' ) ) {
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
}
