<?php
/**
 * A template part for the author box settings on the user profile screens.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */
?>
<h3><?php esc_attr_e( 'Author Box Settings', 'carelib' ); ?></h3>

<p><span class="description"><?php esc_attr_e( 'Choose where you would like to display an author box.', 'carelib' ); ?></span></p>

<?php wp_nonce_field( 'carelib_author_box_nonce', 'toggle_author_box' ); ?>

<table class="form-table">
	<tbody>
		<tr>
			<td>
				<label for="carebox[carelib_author_box_single]">
					<input id="carebox[carelib_author_box_single]" name="carebox[carelib_author_box_single]" type="checkbox" value="1" <?php checked( $single_box ); ?> />
					<?php esc_attr_e( 'Enable Author Box on this User\'s Posts?', 'carelib' ); ?>
				</label><br />

				<label for="carebox[carelib_author_box_archive]">
					<input id="carebox[carelib_author_box_archive]" name="carebox[carelib_author_box_archive]" type="checkbox" value="1" <?php checked( $archive_box ); ?> />
					<?php esc_attr_e( 'Enable Author Box on this User\'s Archives?', 'carelib' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>
