<?php
/**
 * Template part for the WordPress Theme Dashboard page.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */
?>
<div id="theme-dashboard" class="wrap theme-dashboard">

	<section id="dashboard-top" class="dashboard-top">
		<?php do_action( "{$GLOBALS['carelib_prefix']}_dashboard_top" ); ?>
	</section><!-- #dashboard-top -->

	<div id="dashboard-container" class="dashboard-contaner">

		<ul id="dashboard-menu" class="dashboard-menu">
			<?php do_action( "{$GLOBALS['carelib_prefix']}_dashboard_menu_items" ); ?>
		</ul><!-- #dashboard-menu -->

		<section id="dashboard-content" class="dashboard-content">
			<?php do_action( "{$GLOBALS['carelib_prefix']}_dashboard_content" ); ?>
		</section><!-- #dashboard-content -->

	</div><!-- #dashboard-container -->

	<section id="dashboard-sidebar" class="dashboard-sidebar">
		<?php do_action( "{$GLOBALS['carelib_prefix']}_dashboard_sidebar" ); ?>
	</section><!-- #dashboard-sidebar -->

</div><!-- #theme-dashboard -->
