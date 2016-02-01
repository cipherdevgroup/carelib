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
<div class="buttonset">
	<?php foreach ( $post_layouts as $layout ) : ?>
		<?php $post_types = $layout->get_post_types(); ?>
		<?php if ( true === $layout->is_post() && $layout->get_image() && ! ( ! empty( $post_types ) && ! in_array( $post->post_type, $layout->get_post_types() ) ) ) : ?>

			<label>
				<input type="radio" value="<?php echo esc_attr( $layout->get_name() ); ?>" name="carelib-post-layout" <?php checked( $post_layout, $layout->get_name() ); ?> />

				<span class="screen-reader-text"><?php echo esc_html( $layout->get_label() ); ?></span>

				<img src="<?php echo esc_url( sprintf( $layout->get_image(), get_template_directory_uri(), get_stylesheet_directory_uri() ) ); ?>" alt="<?php echo esc_attr( $layout->get_label() ); ?>" />
			</label>

		<?php endif; ?>

	<?php endforeach; ?>
</div>
<?php wp_nonce_field( "{$GLOBALS['carelib_prefix']}_update_post_layout", "{$GLOBALS['carelib_prefix']}_post_layout_nonce" ); ?>
