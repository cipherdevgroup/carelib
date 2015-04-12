<?php
/**
 * General theme helper functions.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */

/**
 * A class to register settings and load templates for author boxes.
 *
 * @package SiteCareLibrary
 */
class SiteCare_Author_Box {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   SiteCare_Author_Box::$wp_hooks
	 * @return void
	 */
	public function run() {
		self::wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	private function wp_hooks() {
		add_filter( 'hybrid_attr_author-box', array( $this, 'attr_author_box' ), 10, 2 );
		add_action( 'tha_entry_after',        array( $this, 'single' ) );
		add_action( 'tha_content_top',        array( $this, 'archive' ) );
	}

	/**
	 * Author box attributes.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $attr
	 * @param  string $context
	 * @return array
	 */
	public function attr_author_box( $attr, $context ) {
		$class      = 'author-box';
		$attr['id'] = 'author-box';

		if ( ! empty( $context ) ) {
			$attr['id'] = "author-box-{$context}";
			$class    .= " author-box-{$context}";
		}

		$attr['class']     = $class;
		$attr['itemscope'] = 'itemscope';
		$attr['itemtype']  = 'http://schema.org/Person';
		$attr['itemprop']  = 'author';

		return $attr;
	}

	/**
	 * Displays the single author box using a template.
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   locate_template() Load the single author box template.
	 * @return void
	 */
	public function single() {
		if ( ! is_singular( apply_filters( 'sitecare_author_box_types', array( 'post' ) ) ) ) {
			return;
		}

		$display = get_the_author_meta( 'sitecare_author_box_single' );

		// Bail if display is disabled. Continue if no author meta exists.
		if ( '' !== $display && '0' === "$display" ) {
			return;
		}

		// Use the theme's single author box template if it exists.
		if ( '' !== locate_template( 'sitecare/author-box-single.php' ) ) {
			return require_once locate_template( 'sitecare/author-box-single.php' );
		}
		require_once sitecare_library()->dir . 'templates/author-box-single.php';
	}

	/**
	* Displays the archive author box using a template.
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   locate_template() Load the archive author box template.
	 * @return void
	 */
	public function archive() {
		if ( ! is_author() || is_paged() ) {
			return;
		}
		$display = get_the_author_meta( 'sitecare_author_box_archive' );

		// Bail if display is disabled or no author meta exists.
		if ( empty( $display ) ) {
			return;
		}

		// Use the theme's archive author box template if it exists.
		if ( '' !== locate_template( 'sitecare/author-box-archive.php' ) ) {
			return require_once locate_template( 'sitecare/author-box-archive.php' );
		}
		require_once sitecare_library()->dir . 'templates/author-box-archive.php';
	}

}
