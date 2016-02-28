<?php
/**
 * A template part for displaying the post layout metabox.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

?>
<?php
	wp_nonce_field(
		"{$GLOBALS['carelib_prefix']}_update_post_layout",
		"{$GLOBALS['carelib_prefix']}_post_layout_nonce"
	);
?>

<div class="buttonset">
	<?php foreach ( carelib_get_layouts() as $layout ) : ?>

		<?php if ( carelib_layout_has_post_metabox( $layout, $post->post_type ) ) : ?>

			<?php require carelib_get_dir( 'admin/templates/layout-select.php' ); ?>

		<?php endif; ?>

	<?php endforeach; ?>
</div>
