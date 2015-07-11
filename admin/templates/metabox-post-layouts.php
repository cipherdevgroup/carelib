<?php
/**
 * A template part for displaying the post layout metabox.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */
?>
<div class="buttonset">
	<?php foreach ( $post_layouts as $layout ) : ?>

		<?php if ( true === $layout->is_post_layout && $layout->image && ! ( ! empty( $layout->post_types ) && ! in_array( $post->post_type, $layout->post_types ) ) ) : ?>

			<label>
				<input type="radio" value="<?php echo esc_attr( $layout->name ); ?>" name="carelib-post-layout" <?php checked( $post_layout, $layout->name ); ?> />

				<span class="screen-reader-text"><?php echo esc_html( $layout->label ); ?></span>

				<img src="<?php echo esc_url( sprintf( $layout->image, get_template_directory_uri(), get_stylesheet_directory_uri() ) ); ?>" alt="<?php echo esc_attr( $layout->label ); ?>" />
			</label>

		<?php endif; ?>

	<?php endforeach; ?>
</div>
<?php wp_nonce_field( "{$this->prefix}_update_post_layout", "{$this->prefix}_post_layout_nonce" ); ?>
