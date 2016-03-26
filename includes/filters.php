<?php
/**
 * All the default filters within the library.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

global $wp_embed, $carelib_prefix;

// Don't strip tags on single post titles.
remove_filter( 'single_post_title', 'strip_tags' );

// Filters the title for untitled posts.
add_filter( 'the_title', 'carelib_untitled_post' );

// Filters the archive title and description.
add_filter( 'get_the_archive_title',       'carelib_archive_title',       5 );
add_filter( 'get_the_archive_description', 'carelib_archive_description', 5 );

carelib_add_the_content_filters( "{$carelib_prefix}_archive_description" );

// Default excerpt more.
add_filter( 'excerpt_more',          'carelib_excerpt_more', 5 );
add_filter( 'the_content_more_link', 'carelib_excerpt_more', 5 );

// Add an itemprop of "image" to WordPress attachment images.
add_filter( 'wp_get_attachment_image_attributes', 'carelib_attachment_image_itemprop' );

// Modifies the arguments and output of wp_link_pages().
add_filter( 'wp_link_pages_args', 'carelib_link_pages_args', 5 );
add_filter( 'wp_link_pages_link', 'carelib_link_pages_link', 5 );

// Filters to add microdata support to common template tags.
add_filter( 'the_author_posts_link',          'carelib_the_author_posts_link',          5 );
add_filter( 'get_comment_author_link',        'carelib_get_comment_author_link',        5 );
add_filter( 'get_comment_author_url_link',    'carelib_get_comment_author_url_link',    5 );
add_filter( 'get_avatar',                     'carelib_get_avatar',                     5 );

add_filter( "{$carelib_prefix}_attr_head",           'carelib_attr_head',           5 );
add_filter( "{$carelib_prefix}_attr_body",           'carelib_attr_body',           5 );
add_filter( "{$carelib_prefix}_attr_site-container", 'carelib_attr_site_container', 5 );
add_filter( "{$carelib_prefix}_attr_site-inner",     'carelib_attr_site_inner',     5 );
add_filter( "{$carelib_prefix}_attr_site-footer",    'carelib_attr_site_footer',    5 );
add_filter( "{$carelib_prefix}_attr_content",        'carelib_attr_content',        5 );
add_filter( "{$carelib_prefix}_attr_skip-link",      'carelib_attr_skip_link',      5, 2 );
add_filter( "{$carelib_prefix}_attr_sidebar",        'carelib_attr_sidebar',        5, 2 );
add_filter( "{$carelib_prefix}_attr_menu-toggle",    'carelib_attr_menu_toggle',    5, 2 );
add_filter( "{$carelib_prefix}_attr_menu",           'carelib_attr_menu',           5, 2 );
add_filter( "{$carelib_prefix}_attr_nav",            'carelib_attr_nav',            5, 2 );
add_filter( "{$carelib_prefix}_attr_footer-widgets", 'carelib_attr_footer_widgets', 5, 2 );

// Header attributes.
add_filter( "{$carelib_prefix}_attr_site-header",      'carelib_attr_site_header',      5 );
add_filter( "{$carelib_prefix}_attr_site-branding",    'carelib_attr_site_branding',    5 );
add_filter( "{$carelib_prefix}_attr_site-title",       'carelib_attr_site_title',       5 );
add_filter( "{$carelib_prefix}_attr_site-description", 'carelib_attr_site_description', 5 );

// Archive page header attributes.
add_filter( "{$carelib_prefix}_attr_archive-header",      'carelib_attr_archive_header',      5 );
add_filter( "{$carelib_prefix}_attr_archive-title",       'carelib_attr_archive_title',       5 );
add_filter( "{$carelib_prefix}_attr_archive-description", 'carelib_attr_archive_description', 5 );

// Post-specific attributes.
add_filter( "{$carelib_prefix}_attr_post",            'carelib_attr_post',            5 );
add_filter( "{$carelib_prefix}_attr_entry",           'carelib_attr_post',            5 ); // Alternate for "post".
add_filter( "{$carelib_prefix}_attr_entry-title",     'carelib_attr_entry_title',     5 );
add_filter( "{$carelib_prefix}_attr_entry-author",    'carelib_attr_entry_author',    5 );
add_filter( "{$carelib_prefix}_attr_entry-published", 'carelib_attr_entry_published', 5 );
add_filter( "{$carelib_prefix}_attr_entry-content",   'carelib_attr_entry_content',   5 );
add_filter( "{$carelib_prefix}_attr_entry-summary",   'carelib_attr_entry_summary',   5 );
add_filter( "{$carelib_prefix}_attr_entry-terms",     'carelib_attr_entry_terms',     5, 2 );

// Comment specific attributes.
add_filter( "{$carelib_prefix}_attr_comment",           'carelib_attr_comment',           5 );
add_filter( "{$carelib_prefix}_attr_comment-author",    'carelib_attr_comment_author',    5 );
add_filter( "{$carelib_prefix}_attr_comment-published", 'carelib_attr_comment_published', 5 );
add_filter( "{$carelib_prefix}_attr_comment-permalink", 'carelib_attr_comment_permalink', 5 );
add_filter( "{$carelib_prefix}_attr_comment-content",   'carelib_attr_comment_content',   5 );

add_filter( 'template_include',    'carelib_index_include',      95 );
add_filter( 'single_template',     'carelib_singular_template',   5 );
add_filter( 'page_template',       'carelib_singular_template',   5 );
add_filter( 'front_page_template', 'carelib_front_page_template', 5 ); // Doesn't work b/c bug with get_query_template().
add_filter( 'frontpage_template',  'carelib_front_page_template', 5 );
add_filter( 'comments_template',   'carelib_comments_template',   5 );

add_filter( 'body_class',    'carelib_body_class_filter', 0, 2 );
add_filter( 'post_class',    'carelib_post_class_filter', 0, 3 );
add_filter( 'comment_class', 'carelib_comment_class_filter', 0, 3 );

add_filter( 'stylesheet_uri',        'carelib_min_stylesheet_uri',    5, 2 );
add_filter( 'stylesheet_uri',        'carelib_style_filter',         15 );
add_filter( 'locale_stylesheet_uri', 'carelib_locale_stylesheet_uri', 5 );

add_filter( 'get_search_form', 'carelib_search_form_get_form', 99 );

add_filter( 'mce_buttons', 'carelib_tinymce_add_styleselect', 99 );
add_filter( 'mce_buttons_2', 'carelib_tinymce_disable_styleselect', 99 );
add_filter( 'tiny_mce_before_init', 'carelib_tinymce_formats', 99 );

add_filter( "{$carelib_prefix}_get_theme_layout", 'carelib_filter_layout', 5 );
