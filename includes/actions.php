<?php
/**
 * All the default actions within the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

add_action( 'save_post',         'carelib_delete_image_cache_by_post', 10 );
add_action( 'deleted_post_meta', 'carelib_delete_image_cache_by_meta', 10, 2 );
add_action( 'updated_post_meta', 'carelib_delete_image_cache_by_meta', 10, 2 );
add_action( 'added_post_meta',   'carelib_delete_image_cache_by_meta', 10, 2 );

add_action( 'wp_head', 'carelib_meta_charset',  0 );
add_action( 'wp_head', 'carelib_meta_viewport', 1 );
add_action( 'wp_head', 'carelib_link_pingback', 2 );
add_action( 'wp_head', 'carelib_canihas_js',    3 );

add_action( 'after_setup_theme',      'carelib_load_locale_functions',   0 );
add_action( 'after_setup_theme',      'carelib_load_textdomains',        5 );

add_action( 'init', 'carelib_register_post_template_meta', 15 );
add_action( 'init', 'carelib_register_layouts_meta', 15 );
add_action( 'init', 'carelib_register_post_style_meta', 15 );

add_action( 'wp_enqueue_scripts', 'carelib_enqueue_scripts', 5 );

add_action( 'widgets_init', '__return_false', 95 );

remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action( 'wp_enqueue_scripts', 'carelib_register_styles', 0 );

add_action( 'after_setup_theme', 'carelib_theme_support',     12 );
add_action( 'init',              'carelib_post_type_support', 15 );
add_action( 'init', 'carelib_do_register_layouts', 95 );
