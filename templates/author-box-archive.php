<?php
/**
 * The default author box template for archives.
 *
 * If you need to make changes to this template, copy it into your theme or
 * child theme in the following format: '/carelib/author-box-archive.php'.
 *
 * @package     CareLib
 * @subpackage  HybridCore
 * @copyright   Copyright (c) 2015, WP Site Care, LLC
 * @license     GPL-2.0+
 * @since       0.1.0
 */
?>
<section <?php hybrid_attr( 'author-box', 'archive' ); ?>>

	<div class="avatar-wrap">
		<?php echo get_avatar( get_the_author_meta( 'email' ), 100, '', get_the_author() ); ?>
	</div><!-- .avatar-wrap -->

	<div class="author-info">

		<h3 class="author-box-title">
			<?php _e( 'Written by', 'carelib' ); ?> <?php the_author_posts_link(); ?>
		</h3>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<div class="description" itemprop="description">
				<?php echo wpautop( get_the_author_meta( 'description' ) ) ?>
			</div>
		<?php endif; ?>

	</div><!-- .author-info -->

</section><!-- .author-box -->
