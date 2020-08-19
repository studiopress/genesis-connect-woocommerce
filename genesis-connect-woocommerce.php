<?php
/**
 * Plugin Name: Genesis Connect for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/genesis-connect-woocommerce/
 * Version: 1.1.0
 * Author: StudioPress
 * Author URI: https://www.studiopress.com/
 * Description: Allows you to seamlessly integrate WooCommerce with the Genesis Framework and Genesis child themes.
 * Text Domain: gencwooc
 * License: GNU General Public License v2.0 (or later)
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.6.4
 *
 * @package Genesis_Connect_WooCommerce
 *
 * Special thanks to Ade Walker (http://www.studiograsshopper.ch/) for his contributions to this plugin.
 */

define( 'GCW_DIR', dirname( __FILE__ ) );
define( 'GCW_TEMPLATE_DIR', dirname( __FILE__ ) . '/templates' );
define( 'GCW_LIB_DIR', dirname( __FILE__ ) . '/lib' );
define( 'GCW_ADMIN_DIR', dirname( __FILE__ ) . '/admin' );
define( 'GCW_WIDGETS_DIR', dirname( __FILE__ ) . '/widgets' );
define( 'GCW_SP_DIR', dirname( __FILE__ ) . '/sp-plugins-integration' );

add_action( 'after_setup_theme', 'gencwooc_setup' );
/**
 * Setup Genesis Connect for WooCommerce.
 *
 * Checks whether WooCommerce is active.
 * Once past these checks, loads the necessary files, actions and filters for the plugin
 * to do its thing.
 *
 * @since 0.9.0
 */
function gencwooc_setup() {

	require_once GCW_ADMIN_DIR . '/notices.php';

	$ready = true;

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
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

	require_once GCW_LIB_DIR . '/template-loader.php';
	require_once GCW_LIB_DIR . '/posts-per-page.php';
	require_once GCW_LIB_DIR . '/widgets.php';

	if ( ! current_theme_supports( 'gencwooc-woo-breadcrumbs' ) ) {
		require_once GCW_LIB_DIR . '/breadcrumb.php';
	}

	add_theme_support( 'woocommerce' );

	add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-scripts', 'genesis-seo' ) );
	add_post_type_support( 'product', array( 'genesis-simple-sidebars', 'genesis-simple-menus' ) );

	if ( current_theme_supports( 'gencwooc-featured-products-widget' ) ) {
		require_once GCW_WIDGETS_DIR . '/class-gencwooc-featured-products.php';
	}

	remove_filter( 'template_include', array( &$woocommerce, 'template_loader' ) );
	add_filter( 'template_include', 'gencwooc_template_loader', 20 );

	if ( is_plugin_active( 'genesis-simple-sidebars/plugin.php' ) ) {
		require_once GCW_SP_DIR . '/genesis-simple-sidebars.php';
	}

	if ( is_plugin_active( 'genesis-simple-menus/simple-menu.php' ) ) {
		require_once GCW_SP_DIR . '/genesis-simple-menus.php';
	}

}

add_action( 'plugins_loaded', 'gencwooc_load_plugin_textdomain' );
/**
 * Load plugin translated strings.
 *
 * Callback for WordPress 'plugins_loaded' action.
 *
 * @uses load_plugin_textdomain()
 * @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
 *
 * @since 1.1.0
 */
function gencwooc_load_plugin_textdomain() {
	load_plugin_textdomain( 'gencwooc', false, GCW_DIR . '/languages' );
}
