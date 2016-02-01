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

add_action( 'load-post.php',     'carelib_metabox_post_layouts_actions' );
add_action( 'load-post-new.php', 'carelib_metabox_post_layouts_actions' );
