<?php
/**
 * A template part for displaying the post style selection metabox.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */
?>
<p>
	<select name="carelib-post-style" class="widefat">
		<option value=""></option>
		<?php foreach ( $styles as $label => $file ) : ?>
			<option value="<?php echo esc_attr( $file ); ?>" <?php selected( $post_style, $file ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</p>
<?php wp_nonce_field( 'carelib_update_post_style', 'carelib-post-style-nonce' ); ?>
