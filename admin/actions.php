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
add_action( 'add_meta_boxes',    'carelib_maybe_disable_post_layout_metabox', 5, 2 );

add_action( 'init', 'carelib_maybe_add_theme_dashboard' );
