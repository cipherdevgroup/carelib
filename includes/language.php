<?php
/**
 * Internationalization and translation functions.
 *
 * This file provides a few functions for use by theme authors. It also handles
 * properly loading translation files for both the parent and child themes.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Gets the parent theme textdomain. This allows the library to recognize
 * the proper textdomain of the parent theme.
 *
 * @since  0.2.0
 * @access protected
 * @return string The textdomain of the theme.
 */
function _carelib_get_parent_textdomain() {
	$domain = apply_filters( "{$GLOBALS['carelib_prefix']}_parent_textdomain", '' );

	// If the textdomain has been set, return it.
	if ( ! empty( $domain ) ) {
		return sanitize_key( $domain );
	}

	$theme  = carelib_get_parent();
	$domain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_template();

	return sanitize_key( $domain );
}

/**
 * Gets the child theme textdomain. This allows the library to recognize
 * the proper textdomain of the child theme.
 *
 * @since  0.2.0
 * @access protected
 * @return string The textdomain of the child theme.
 */
function _carelib_get_child_textdomain() {
	if ( ! is_child_theme() ) {
		return '';
	}

	$domain = apply_filters( "{$GLOBALS['carelib_prefix']}_child_textdomain", '' );

	// If the textdomain has been set, return it.
	if ( ! empty( $domain ) ) {
		return sanitize_key( $domain );
	}

	$theme  = carelib_get_theme();
	$domain = $theme->get( 'TextDomain' ) ? $theme->get( 'TextDomain' ) : get_stylesheet();

	return sanitize_key( $domain );
}

/**
 * Returns the parent theme domain path. No slash.
 *
 * @since  0.2.0
 * @access protected
 * @return string
 */
function _carelib_get_parent_domain_path() {
	if ( file_exists( carelib_get_parent_dir( 'languages' ) ) ) {
		return 'languages';
	}
	$theme = carelib_get_parent();

	return $theme->get( 'DomainPath' ) ? trim( $theme->get( 'DomainPath' ), '/' ) : 'languages';
}

/**
 * Returns the child theme domain path. No slash.
 *
 * @since  0.2.0
 * @access protected
 * @return string
 */
function _carelib_get_child_domain_path() {
	if ( ! is_child_theme() ) {
		return '';
	}
	if ( file_exists( carelib_get_child_dir( 'languages' ) ) ) {
		return 'languages';
	}
	$theme = carelib_get_theme();

	return $theme->get( 'DomainPath' ) ? trim( $theme->get( 'DomainPath' ), '/' ) : 'languages';
}

/**
 * Loads a `/languages/{$locale}.php` file for specific locales.
 *
 * `$locale` should be an all lowercase and hyphenated (as opposed to an
 * underscore) file name. So, an `en_US` locale would be `en-us.php`.
 *
 * Also note that the child theme locale file will load **before** the
 * parent theme locale file. This is standard practice in core WP for
 * allowing pluggable functions if a theme author so desires.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_load_locale_functions() {
	$locale = strtolower( str_replace( '_', '-', get_locale() ) );

	// Define locale functions files.
	$child_func = carelib_get_child_dir()  . _carelib_get_child_domain_path()  . "/{$locale}.php";
	$theme_func = carelib_get_parent_dir() . _carelib_get_parent_domain_path() . "/{$locale}.php";

	// If file exists in child theme.
	if ( is_child_theme() && file_exists( $child_func ) ) {
		require_once( $child_func );
	}

	// If file exists in parent theme.
	if ( file_exists( $theme_func ) ) {
		require_once( $theme_func );
	}
}

/**
 * Load the theme, child theme, and library textdomains automatically.
 * No need for theme authors to do this.
 *
 * This also utilizes the `Domain Path` header from `style.css`. It defaults
 * to the `languages` folder.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function carelib_load_textdomains() {
	// Load theme textdomain.
	load_theme_textdomain(
		_carelib_get_parent_textdomain(),
		carelib_get_parent_dir() . _carelib_get_parent_domain_path()
	);

	// Load child theme textdomain.
	if ( is_child_theme() ) {
		load_child_theme_textdomain(
			_carelib_get_child_textdomain(),
			carelib_get_child_dir() . _carelib_get_child_domain_path()
		);
	}
}

/**
 * Filter the 'load_textdomain_mofile' filter hook so that we can change
 * the directory and file name of the mofile for translations.
 *
 * This allows child themes to have a folder called /languages with
 * translations of their parent theme so that the translations aren't lost
 * on a parent theme upgrade.
 *
 * @since  0.2.0
 * @access public
 * @param  string $mofile File name of the .mo file.
 * @param  string $domain The textdomain currently being filtered.
 * @return string
 */
function carelib_load_textdomain_mofile( $mofile, $domain ) {
	// If the $domain is for the parent or child theme, search for a $domain-$locale.mo file.
	if ( _carelib_get_parent_textdomain() === $domain || _carelib_get_child_textdomain() === $domain ) {

		// Get the locale.
		$locale = get_locale();

		// Get just the theme path and file name for the mofile.
		$mofile_short = str_replace( "{$locale}.mo", "{$domain}-{$locale}.mo", $mofile );
		$mofile_short = str_replace( array( carelib_get_parent_dir(), carelib_get_child_dir() ), '', $mofile_short );

		// Attempt to find the correct mofile.
		$locate_mofile = locate_template( array( $mofile_short ) );

		// Return the mofile.
		return $locate_mofile ? $locate_mofile : $mofile;
	}

	return $mofile;
}

/**
 * Gets the language for the currently-viewed page. It strips the region
 * from the locale if needed and just returns the language code.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function carelib_get_language( $locale = '' ) {
	if ( empty( $locale ) ) {
		$locale = get_locale();
	}

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page. It strips the language
 * from the locale if needed. Note that not all locales will have a region,
 * so this might actually return the same thing as `get_language()`.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $locale
 * @return string
 */
function carelib_get_region( $locale = '' ) {
	if ( empty( $locale ) ) {
		$locale = get_locale();
	}

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}
