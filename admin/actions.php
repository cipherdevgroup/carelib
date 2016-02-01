<?php
/**
 * Methods for handling admin JavaScript and CSS in the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_enqueue_scripts',  'carelib_admin_register_scripts', 0 );
add_action( 'admin_enqueue_scripts', 'carelib_admin_register_styles', 0 );
add_action( 'load-post.php',     'carelib_metabox_post_template_actions' );
add_action( 'load-post-new.php', 'carelib_metabox_post_template_actions' );
add_action( 'load-post.php',     'carelib_metabox_post_styles_actions' );
add_action( 'load-post-new.php', 'carelib_metabox_post_styles_actions' );
add_action( 'load-post.php',     'carelib_metabox_post_layouts_actions' );
add_action( 'load-post-new.php', 'carelib_metabox_post_layouts_actions' );

add_action( 'admin_menu',            'carelib_dashboard_menu',      0 );
add_action( 'after_switch_theme',    'carelib_dashboard_setup',    10 );
add_action( 'after_switch_theme',    'carelib_dashboard_redirect', 12 );
add_action( 'switch_theme',          'carelib_dashboard_cleanup',  10 );
add_action( 'admin_enqueue_scripts', 'carelib_dashboard_scripts',  10 );
add_action( 'admin_notices',         'carelib_dashboard_notices',  10 );
