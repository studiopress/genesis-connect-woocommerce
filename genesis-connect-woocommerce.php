<?php
/*
Plugin Name: Genesis Connect for WooCommerce
Plugin URI: https://wordpress.org/plugins/genesis-connect-woocommerce/
Version: 1.0
Author: StudioPress
Author URI: https://www.studiopress.com/
Description: Allows you to seamlessly integrate WooCommerce with the Genesis Framework and Genesis child themes.
WC requires at least: 3.3.0
WC tested up to: 3.4

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php

Special thanks to Ade Walker (http://www.studiograsshopper.ch/) for his contributions to this plugin.
*/

/** Define the Genesis Connect for WooCommerce constants */
define( 'GCW_TEMPLATE_DIR', dirname( __FILE__ ) . '/templates' );
define( 'GCW_LIB_DIR', dirname( __FILE__ ) . '/lib');
define( 'GCW_ADMIN_DIR', dirname( __FILE__ ) . '/admin');
define( 'GCW_WIDGETS_DIR', dirname( __FILE__ ) . '/widgets' );
define( 'GCW_SP_DIR', dirname( __FILE__ ) . '/sp-plugins-integration' );

add_action( 'after_setup_theme', 'gencwooc_setup' );
/**
 * Setup GCW
 *
 * Checks whether WooCommerce is active, then checks if relevant
 * theme support exists. Once past these checks, loads the necessary
 * files, actions and filters for the plugin to do its thing.
 *
 * @since 0.9.0
 */
function gencwooc_setup() {

	require_once GCW_ADMIN_DIR . '/notices.php';
	$ready = true;

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_action( 'admin_notices', 'gencwooc_woocommerce_notice' );
		$ready = false;
	}

	if ( ! function_exists( 'genesis' ) ) {
		if ( ! is_multisite() ) {
			add_action( 'admin_notices', 'gencwooc_genesis_notice' );
		}

		$ready = false;
	}

	if ( ! $ready ) {
		return;
	}

	global $woocommerce;

	/** Load GCW files */
	require_once( GCW_LIB_DIR . '/template-loader.php' );

	// Load posts per page option
	require_once( GCW_LIB_DIR . '/posts-per-page.php' );

	/** Load modified Genesis breadcrumb filters and callbacks */
	if ( ! current_theme_supports( 'gencwooc-woo-breadcrumbs') )
		require_once( GCW_LIB_DIR . '/breadcrumb.php' );

	/** Ensure WooCommerce 2.0+ compatibility */
	add_theme_support( 'woocommerce' );

	/** Add Genesis Layout, Genesis Scripts and SEO options to Product edit screen */
	add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-scripts', 'genesis-seo' ) );

	/** Add Studiopress plugins support */
	add_post_type_support( 'product', array( 'genesis-simple-sidebars', 'genesis-simple-menus' ) );

	/** Add Widgets */
	if ( current_theme_supports( 'gencwooc-featured-products-widget' ) ) {
		require_once( GCW_WIDGETS_DIR . '/woocommerce-featured-widgets.php' );
	}

	/** Take control of shop template loading */
	remove_filter( 'template_include', array( &$woocommerce, 'template_loader' ) );
	add_filter( 'template_include', 'gencwooc_template_loader', 20 );

	/** Integration - Genesis Simple Sidebars */
	if ( is_plugin_active( 'genesis-simple-sidebars/plugin.php' ) ) {
		require_once( GCW_SP_DIR . '/genesis-simple-sidebars.php' );
	}

	/** Integration - Genesis Simple Menus */
	if ( is_plugin_active( 'genesis-simple-menus/simple-menu.php' ) ) {
		require_once( GCW_SP_DIR . '/genesis-simple-menus.php' );
	}

}
