<?php
/**
 * Template part for the WordPress Theme Dashboard page.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */
?>
<div id="carelib-dashboard" class="wrap carelib-dashboard">

	<section id="dashboard-top" class="dashboard-top">
		<?php do_action( "{$this->prefix}_dashboard_top" ); ?>
	</section><!-- #dashboard-top -->

	<div id="dashboard-container" class="dashboard-contaner">

		<ul id="dashboard-menu" class="dashboard-menu">
			<?php do_action( "{$this->prefix}_dashboard_menu_items" ); ?>
		</ul><!-- #dashboard-menu -->

		<section id="dashboard-content" class="dashboard-content visible">
			<?php do_action( "{$this->prefix}_dashboard_content" ); ?>
		</section><!-- #dashboard-content -->

		<section id="dashboard-sidebar" class="dashboard-sidebar">
			<?php do_action( "{$this->prefix}_dashboard_sidebar" ); ?>
		</section><!-- #dashboard-sidebar -->

	</div><!-- #dashboard-container -->

</div><!-- #carelib-dashboard -->
