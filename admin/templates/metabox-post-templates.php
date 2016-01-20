<?php
/**
 * A template part for displaying the post template selection metabox.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */
?>
<p>
	<select name="carelib-post-template" class="widefat">
		<option value=""></option>
		<?php foreach ( $templates as $label => $template ) : ?>
			<option value="<?php echo esc_attr( $template ); ?>" <?php selected( $post_template, $template ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</p>
<?php wp_nonce_field( "{$this->prefix}_update_post_template", "{$this->prefix}_post_template_nonce" ); ?>
