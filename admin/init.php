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

/**
 * Class for common theme admin functionality.
 *
 * @version 0.1.0
 */
class CareLib_Admin {

	/**
	 * Placeholder for our author box admin class instance.
	 *
	 * @since 0.1.0
	 * @var   CareLib_Author_Box_Admin
	 */
	public $author_box;

	/**
	 * Placeholder for our Dashboard class instance.
	 *
	 * @since 0.2.0
	 * @var   CareLib_Dashboard
	 */
	public $dashboard;

	/**
	 * Placeholder for our TinyMCE admin class instance.
	 *
	 * @since 0.2.0
	 * @var   CareLib_TinyMCE_Admin
	 */
	public $tinymce;

	/**
	 * Slashed directory path to the admin directory.
	 *
	 * @since 0.1.0
	 * @type  string
	 */
	protected $dir;

	/**
	 * Get our current logo settings stored in options.
	 *
	 * @uses get_option()
	 */
	public function __construct() {
		$this->dir = carelib()->get_lib_dir() . 'admin/';
		self::includes();
		self::instantiate();
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
	 * Include admin library files.
	 *
	 * @since   0.1.0
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		require_once $this->dir . 'class-tiny-mce.php';
		require_once $this->dir . 'class-dashboard.php';
		require_once $this->dir . 'class-author-box.php';
	}

	/**
	 * Spin up instances of our admin classes once they've been included.
	 *
	 * @since   0.1.0
	 * @access  private
	 * @return  void
	 */
	private function instantiate() {
		$this->author_box = new CareLib_Author_Box_Admin;
		$this->dashboard  = new CareLib_Dashboard;
		$this->tinymce    = new CareLib_TinyMCE_Admin;

		$this->author_box->run();
		$this->dashboard->run();
		$this->tinymce->run();
	}

}
