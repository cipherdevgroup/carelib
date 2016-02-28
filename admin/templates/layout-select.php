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
<label>
	<span class="screen-reader-text"><?php echo esc_html( $layout->get_label() ); ?></span>

	<input type="radio" value="<?php echo esc_attr( $layout->get_name() ); ?>" name="carelib-post-layout" <?php checked( $current_layout, $layout->get_name() ); ?> />

	<img src="<?php echo esc_url( sprintf( $layout->get_image(), get_template_directory_uri(), get_stylesheet_directory_uri() ) ); ?>" alt="<?php echo esc_attr( $layout->get_label() ); ?>" />
</label>
