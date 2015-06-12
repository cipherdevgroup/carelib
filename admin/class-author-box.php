<?php
/**
 * General theme helper functions.
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
 * A class to register settings and load templates for author boxes.
 *
 * @package CareLib
 */
class CareLib_Author_Box_Admin {

	/**
	 * Get our class up and running!
	 *
	 * @since  0.1.0
	 * @access public
	 * @uses   CareLib_Author_Box::$wp_hooks
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
		add_filter( 'user_contactmethods',      array( $this, 'user_contactmethods' ) );
		add_action( 'show_user_profile',        array( $this, 'user_fields' ) );
		add_action( 'edit_user_profile',        array( $this, 'user_fields' ) );
		add_action( 'personal_options_update',  array( $this, 'meta_save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'meta_save' ) );
	}

	/**
	 * Helper function to determine if an automated task which should prevent
	 * saving meta box data is running.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function stop_save() {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
			defined( 'DOING_AJAX' ) && DOING_AJAX ||
			defined( 'DOING_CRON' ) && DOING_CRON;
	}

	/**
	 * Add additional contact methods for registered users.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array $contactmethods Existing contact methods.
	 * @return array $contactmethods Modifed contact methods.
	 */
	public function user_contactmethods( array $contactmethods ) {
		$contactmethods['googleplus'] = __( 'Google+', 'carelib' );
		$contactmethods['twitter']    = __( 'Twitter (Without @)', 'carelib' );
		$contactmethods['facebook']   = __( 'Facebook', 'carelib' );
		return $contactmethods;
	}

	/**
	 * Add fields for author box settings to the user edit screen.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $user Object WordPress user object.
	 * @return void
	 */
	public function user_fields( $user ) {
		if ( ! current_user_can( 'edit_users', $user->ID ) ) {
			return false;
		}
		$singular_box = get_the_author_meta( 'carelib_author_box_singular', $user->ID );
		$archive_box  = get_the_author_meta( 'carelib_author_box_archive',  $user->ID );
		// Set the single author box to enabled when no author meta has been set.
		if ( '' === $singular_box ) {
			$singular_box = 1;
		}
		require_once carelib()->get_lib_dir() . 'admin/templates/settings-author-box.php';
	}

	/**
	 * Update author box user meta when user edit page is saved.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  $user_id integer The current user ID
	 * @return void
	 */
	public function meta_save( $user_id ) {
		if ( $this->stop_save() || ! current_user_can( 'edit_users', $user_id ) ) {
			return;
		}

		$no = 'carelib_author_box_nonce';

		if ( ! isset( $_POST[ $no ] ) || ! wp_verify_nonce( $_POST[ $no ], 'toggle_author_box' ) ) {
			return;
		}

		$defaults = array(
			'carelib_author_box_singular' => 0,
			'carelib_author_box_archive'  => 0,
		);

		if ( ! isset( $_POST['carebox'] ) || ! is_array( $_POST['carebox'] ) ) {
			foreach ( $defaults as $key => $value ) {
				update_user_meta( $user_id, $key, $value );
			}
			return;
		}

		$meta = wp_parse_args( $_POST['carebox'], $defaults );

		foreach ( $meta as $key => $value ) {
			update_user_meta( $user_id, sanitize_key( $key ), absint( $value ) );
		}
	}

}
