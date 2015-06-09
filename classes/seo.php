<?php
/**
 * SEO Adjustments for the Hybrid Core Framework.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.2.0
 */

/**
 * SiteCare SEO class.
 */
class SiteCare_SEO {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
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
		remove_action( 'wp_head', 'hybrid_meta_template', 1 );
		add_filter( 'hybrid_site_title',       array( $this, 'site_title' ),       10 );
		add_filter( 'hybrid_site_description', array( $this, 'site_description' ), 10 );
		add_filter( 'excerpt_more',            array( $this, 'excerpt_more' ),     99 );
	}

	/**
	 * Returns the linked site title wrapped in a `<p>` tag unless on the home page
	 * or the main blog page where no other H1 exists.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $title
	 * @return string
	 */
	public function site_title( $title ) {
		if ( is_front_page() || is_home() ) {
			return $title;
		}
		return str_replace( array( '<h1', '</h1' ), array( '<p', '</p' ), $title );
	}

	/**
	 * Returns the site description wrapped in a `<p>` tag.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $desc
	 * @return string
	 */
	public function site_description( $desc ) {
		return str_replace( array( '<h2', '</h2' ), array( '<p', '</p' ), $desc );
	}

	/**
	 * Filter the default Hybrid more link to add a rel="nofollow" attribute.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $text
	 * @return string
	 */
	public function excerpt_more( $text ) {
		return str_replace( 'class="more-link"', 'rel="nofollow" class="more-link"', $text );
	}
}
