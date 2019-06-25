<?php
/**
 * Genesis Simple Sidebars integration.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 0.9.0
 *
 * Based on Genesis Simple Sidebars (GSS) version 0.9.2.
 *
 * What GCW integration needs to do:
 *  1. Add_post_type_support for 'genesis-simple-sidebars'.
 *  2. Deal with serving correct GSS sidebar(s) for Shop page (product archive).
 *
 * What GCW does:
 *  1. GCW adds post_type_support for GSS - see gencwooc_setup().
 *  2. Intercepts GSS sidebar loading functions, deals with Shop Page,
 * then hands back control of sidebar loading in all other cases to GSS.
 *
 * Note: this file is loaded on the 'after_theme_setup' hook only if GSS is activated.
 * @see gencwooc_setup() in genesis-connect-woocommerce.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'get_header', 'gencwooc_ss_handler', 11 );
/**
 * Take control of GSS sidebar loading.
 *
 * Hooked to 'get_header' with priority of 11 to ensure that GSS's actions, which are unhooked
 * here in this function, have been added and therefore can be removed.
 *
 * Unhooks GSS ss_do_sidebar() and ss_do_sidebar_alt() functions and hooks GCW versions of
 * these functions to the same hooks instead.
 *
 * @see GSS ss_sidebars_init() in genesis-simple-sidebars/plugin.php
 *
 * Note for developers:
 * ====================
 * If you want to do more complex manipulations of sidebars, eg load another one altogether
 * (ie not a GSS sidebar, G Sidebar or G Sidebar Alt), unhook this function and replace it with
 * your own version.
 *
 * @since 0.9.0
 */
function gencwooc_ss_handler() {

	remove_action( 'genesis_sidebar', 'ss_do_sidebar' );
	remove_action( 'genesis_sidebar_alt', 'ss_do_sidebar_alt' );

	add_action( 'genesis_sidebar', 'gencwooc_ss_do_sidebar' );
	add_action( 'genesis_sidebar_alt', 'gencwooc_ss_do_sidebar_alt' );

}


/**
 * Callback for dealing with Primary Sidebar loading.
 *
 * Intercepts GSS code flow, so that Shop page can be dealt with, then hands back control to the
 * GSS function for loading primary sidebars. Effectively, it's just a more complex version
 * of ss_do_sidebar().
 *
 * Checks if we're on the product archive and a GSS sidebar has been assigned in the
 * Shop WP Page editor, then, if both true, loads the relevant GSS sidebar on the Shop Page.
 *
 * If either of the above conditions return false, we load the regular sidebar.
 *
 * @since 0.9.0
 *
 * @uses woocommerce_get_page_id()
 */
function gencwooc_ss_do_sidebar() {

	$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' );
	$_bar    = get_post_meta( $shop_id, '_ss_sidebar', true );

	if ( is_post_type_archive( 'product' ) && $_bar ) {
		dynamic_sidebar( $_bar );
	} else {
		genesis_do_sidebar();
	}

}


/**
 * Callback for dealing with Sidebar Alt loading.
 *
 * Intercepts GSS code flow, so that Shop page can be dealt with, then hands back control to the
 * GSS function for loading secondary sidebars. Effectively, it's just a more complex version
 * of ss_do_sidebar_alt().
 *
 * Checks if we're on the product archive and a GSS sidebar has been assigned in the
 * Shop WP Page editor, then, if both true, loads the relevant GSS sidebar on the Shop Page.
 *
 * If either of the above conditions return false, we load the regular alt sidebar.
 *
 * @since 0.9.0
 *
 * @uses woocommerce_get_page_id()
 */
function gencwooc_ss_do_sidebar_alt() {

	$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' );
	$_bar    = get_post_meta( $shop_id, '_ss_sidebar_alt', true );

	if ( is_post_type_archive( 'product' ) && $_bar ) {
		dynamic_sidebar( $_bar );
	} else {
		genesis_do_sidebar_alt();
	}

}
