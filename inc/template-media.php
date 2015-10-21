<?php
/**
 * Media template functions. These functions are meant to handle various
 * features needed in theme templates for media and attachments.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Template_Media {
	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
	}

	/**
	 * Return the HTML output for media found using the media grabber class.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array
	 * @return string
	 */
	public function media_grabber( $args = array() ) {
		return carelib_get( 'media-grabber', null, $args )->get_media();
	}

	/**
	 * Handles the output of the media for audio attachment posts.
	 * This should be used within The Loop.
	 *
	 * @since  0.2.2
	 * @access public
	 * @return string
	 */
	public function get_audio_attachment() {
		return $this->media_grabber( array( 'type' => 'audio' ) );
	}

	/**
	 * Handles the output of the media for video attachment posts.
	 * This should be used within The Loop.
	 *
	 * @since  0.2.2
	 * @access public
	 * @return string
	 */
	public function get_video_attachment() {
		return $this->media_grabber( array( 'type' => 'video' ) );
	}
}
