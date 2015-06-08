<?php
/**
 * A template part for the author box settings on the user profile screens.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */
?>
<h3><?php _e( 'Author Box Settings', 'sitecare-library' ); ?></h3>

<p><span class="description"><?php _e( 'Choose where you would like to display an author box.', 'sitecare-library' ); ?></span></p>

<?php wp_nonce_field( 'sitecare_author_box_nonce', 'toggle_author_box' ); ?>

<table class="form-table">
	<tbody>
		<tr>
			<td>
				<label for="carebox[sitecare_author_box_single]">
					<input id="carebox[sitecare_author_box_single]" name="carebox[sitecare_author_box_single]" type="checkbox" value="1" <?php checked( $single_box ); ?> />
					<?php _e( 'Enable Author Box on this User\'s Posts?', 'sitecare-library' ); ?>
				</label><br />

				<label for="carebox[sitecare_author_box_archive]">
					<input id="carebox[sitecare_author_box_archive]" name="carebox[sitecare_author_box_archive]" type="checkbox" value="1" <?php checked( $archive_box ); ?> />
					<?php _e( 'Enable Author Box on this User\'s Archives?', 'sitecare-library' ); ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>
