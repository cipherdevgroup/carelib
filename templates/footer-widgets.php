<?php
/**
 * The Default Footer Widgets Sidebar Template.
 *
 * If you need to make changes to this template, copy it into your theme or
 * child theme in the following format: '/sitecare/footer-widgets.php'.
 *
 * @package     SiteCareLibrary
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */
?>
<div <?php hybrid_attr( 'footer-widgets' ); ?>>

	<div <?php hybrid_attr( 'wrap', 'footer-widgets' ); ?>>

		<?php while ( $counter <= absint( $this->footer_widgets[0] ) ) : ?>

			<div <?php hybrid_attr( "footer-widgets-{$counter}" ); ?>>

				<?php if ( is_active_sidebar( "footer-{$counter}" ) ) : ?>

					<?php dynamic_sidebar( "footer-{$counter}" ); ?>

				<?php endif; ?>

			</div><!-- .footer-widgets-<?php echo $counter; ?> -->

			<?php $counter++; ?>

		<?php endwhile; ?>

	</div>

</div>
