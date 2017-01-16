<?php
/*
Plugin Name: Genesis Connect for WooCommerce
Plugin URI: http://www.studiopress.com/plugins/genesis-connect-woocommerce
Version: 0.9.9
Author: StudioPress
Author URI: http://www.studiopress.com/
Description: Allows you to seamlessly integrate WooCommerce with the Genesis Framework and Genesis child themes.

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php

Special thanks to Ade Walker (http://www.studiograsshopper.ch/) for his contributions to this plugin.
*/


register_activation_hook( __FILE__, 'gencwooc_activation' );
/**
 * Check the environment when plugin is activated
 *
 * Requirements:
 * - WooCommerce needs to be installed and activated
 *
 * Note: register_activation_hook() isn't run after auto or manual upgrade, only on activation
 * Note: this version of GCW is based on WooCommerce 2.1+
 *
 * @since 0.9.0
 */
function gencwooc_activation() {

	//* If Genesis is not the active theme, deactivate and die.
	if ( 'genesis' != get_option( 'template' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'gencwooc' ), 'http://my.studiopress.com/themes/genesis/' ) );
	}

}



/** Define the Genesis Connect for WooCommerce constants */
define( 'GCW_TEMPLATE_DIR', dirname( __FILE__ ) . '/templates' );
define( 'GCW_LIB_DIR', dirname( __FILE__ ) . '/lib');
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

	/** Fail silently if WooCommerce is not activated */
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		return;

	/** Environment is OK, let's go! */

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

	/** Add Genesis Layout and SEO options to Product edit screen */
	add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-seo' ) );

	/** Add Studiopress plugins support */
	add_post_type_support( 'product', array( 'genesis-simple-sidebars', 'genesis-simple-menus' ) );

	/** Take control of shop template loading */
	remove_filter( 'template_include', array( &$woocommerce, 'template_loader' ) );
	add_filter( 'template_include', 'gencwooc_template_loader', 20 );

	/** Integration - Genesis Simple Sidebars */
	if ( in_array( 'genesis-simple-sidebars/plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		require_once( GCW_SP_DIR . '/genesis-simple-sidebars.php' );

	/** Integration - Genesis Simple Menus */
	if ( in_array( 'genesis-simple-menus/simple-menu.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		require_once( GCW_SP_DIR . '/genesis-simple-menus.php' );

}
