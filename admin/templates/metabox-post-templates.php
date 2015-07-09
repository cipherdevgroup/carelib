<?php
/**
 * A template part for displaying the post template selection metabox.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */
?>
<p>
	<select name="hybrid-post-template" class="widefat">
		<option value=""></option>
		<?php foreach ( $templates as $label => $template ) : ?>
			<option value="<?php echo esc_attr( $template ); ?>" <?php selected( $post_template, $template ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</p>
<?php wp_nonce_field( 'carelib_update_post_template', 'carelib_post_template_nonce' ); ?>
