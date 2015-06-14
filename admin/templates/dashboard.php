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

	<div id="top-container" class="top-wrap">
		<?php do_action( "{$this->prefix}_dashboard_top" ); ?>
	</div>

	<div id="dashboard-tabs" class="panels">

		<ul id="panel-menu" class="inline-list">
			<?php do_action( "{$this->prefix}_dashboard_menu_items" ); ?>
		</ul>

		<div id="panel" class="panel visible clearfix">
			<?php do_action( "{$this->prefix}_dashboard_content" ); ?>
		</div><!-- #main-panel -->

		<div id="panel-sidebar" class="sidebar">
			<?php do_action( "{$this->prefix}_dashboard_sidebar" ); ?>
		</div><!-- #panel-sidebar -->

	</div><!-- #dashboard-tabs -->

</div><!-- #carelib-dashboard -->
