<?php
/**
 * Load all required library files.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

if ( ! class_exists( 'SiteCare_Library' ) ) {

	/**
	 * Class for common SiteCare theme functionality.
	 *
	 * @version 0.1.0
	 */
	class SiteCare_Library {

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
		public $dir;

		/**
		 * Placeholder for our style builder class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Library
		 */
		public $style_builder;

		/**
		 * Placeholder for our author box class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Library
		 */
		public $author_box;

		/**
		 * Placeholder for our author box admin class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Library
		 */
		public $author_box_admin;

		/**
		 * Placeholder for our breadcrumb display class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Breadcrumb_Display
		 */
		public $breadcrumb_display;

		/**
		 * Placeholder for our footer widgets class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Footer_Widgets
		 */
		public $footer_widgets;

		/**
		 * Placeholder for our site logo class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Site_Logo
		 */
		public $site_logo;

		/**
		 * Static placeholder for our main class instance.
		 *
		 * @since 0.1.0
		 * @var   SiteCare_Library
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
				__( 'Cheatin&#8217; huh?', 'sitecare-library' ),
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
				__( 'Cheatin&#8217; huh?', 'sitecare-library' ),
				'0.1.0'
			);
		}

		/**
		 * Main SiteCare_Library Instance
		 *
		 * Insures that only one instance of SiteCare_Library exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 0.1.0
		 * @static
		 * @uses   SiteCare_Library::includes() Include the required files
		 * @return SiteCare_Library
		 */
		public static function instance( $args = array() ) {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SiteCare_Library ) ) {
				self::$instance = new SiteCare_Library;
				self::$instance->dir = trailingslashit( self::$instance->get_library_directory() );
				self::$instance->prefix = empty( $args['prefix'] ) ? get_template() : sanitize_key( $args['prefix'] );
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
			return apply_filters( 'sitecare_library_is_wpcom', false );
		}

		/**
		 * Return the correct path to the sitecare library directory.
		 *
		 * @since   0.1.0
		 * @access  public
		 * @return  string
		 */
		public function get_library_directory() {
			return dirname( __FILE__ );
		}

		/**
		 * Return the correct path to the sitecare library directory.
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
		 * @uses   SiteCare_Library::normalize_path()
		 * @uses   SiteCare_Library::get_library_directory()
		 * @return string
		 */
		public function get_library_uri() {
			$theme_root  = $this->normalize_path( get_theme_root() );
			$library_dir = $this->normalize_path( $this->get_library_directory() );
			return str_replace( $theme_root, get_theme_root_uri(), $library_dir );
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
		 * loaded you can use the sitecare_library_includes filter to unset it
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
			require_once $this->dir . 'functions/attr.php';
			require_once $this->dir . 'functions/seo.php';
			require_once $this->dir . 'functions/template-entry.php';
			require_once $this->dir . 'functions/template-general.php';
			require_once $this->dir . 'functions/template.php';
		}

		/**
		 * Include extensions init files only when theme support has been added.
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function extensions_includes() {
			if ( current_theme_supports( 'sitecare-author-box' ) ) {
				require_once $this->dir . 'classes/author-box.php';
			}
			if ( current_theme_supports( 'breadcrumb-trail' ) ) {
				require_once $this->dir . 'customizer/classes/breadcrumb-display.php';
			}
			if ( current_theme_supports( 'sitecare-footer-widgets' ) ) {
				require_once $this->dir . 'classes/footer-widgets.php';
			}
			if ( current_theme_supports( 'site-logo' ) ) {
				add_action( 'init', array( $this, 'logo_includes' ), 12 );
			}
		}

		/**
		 * Activate the SiteCare Logo plugin. We need to hook into init in order
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
		 * If for some reason you would prefer not to enable the admin features
		 * in the library, they can be disabled using a filter like so:
		 *
		 * add_filter( 'sitecare_library_disable_admin', '__return_true' );
		 *
		 * @since   0.1.0
		 * @access  private
		 * @return  void
		 */
		private function admin_includes() {
			if ( apply_filters( 'sitecare_library_disable_admin', false ) ) {
				return;
			}
			require_once $this->dir . 'admin/functions/tiny-mce.php';
			if ( current_theme_supports( 'sitecare-author-box' ) ) {
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
			$this->style_builder = new SiteCare_Style_Builder;

			if ( class_exists( 'SiteCare_Author_Box', false ) ) {
				$this->author_box = new SiteCare_Author_Box;
				$this->author_box->run();
			}
			if ( class_exists( 'SiteCare_Breadcrumb_Display', false ) ) {
				$this->breadcrumb_display = new SiteCare_Breadcrumb_Display;
				$this->breadcrumb_display->run();
			}
			if ( class_exists( 'SiteCare_Footer_Widgets', false ) ) {
				$this->footer_widgets = new SiteCare_Footer_Widgets;
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
		 * @uses   SiteCare_Site_Logo::run()
		 * @return object Site_Logo
		 */
		function instantiate_logo() {
			if ( ! class_exists( 'SiteCare_Site_Logo', false ) ) {
				return;
			}
			$this->site_logo = new SiteCare_Site_Logo;
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
			if ( class_exists( 'SiteCare_Author_Box_Admin', false ) ) {
				$this->author_box_admin = new SiteCare_Author_Box_Admin;
				$this->author_box_admin->run();
			}
		}

	}
}

if ( ! function_exists( 'sitecare_library' ) ) {
	/**
	 * Grab an instance of the main library class. If you need to reference a
	 * method in the class for some reason, do it using this function.
	 *
	 * Example:
	 *
	 * <?php sitecare_library()->is_customizer_preview(); ?>
	 *
	 * @since   0.1.0
	 * @return  object SiteCare_Library
	 */
	function sitecare_library() {
		return SiteCare_Library::instance();
	}
}

// Get the library up and running.
sitecare_library();
