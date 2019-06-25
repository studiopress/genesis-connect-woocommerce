<?php
/**
 * Genesis Simple Menus integration.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 0.9.0
 *
 * Genesis Simple Menus (GSM) version 0.1.4
 *
 * What GCW integration needs to do:
 *  1. add_post_type_support for 'genesis-simple-menus'
 *  2. deal with serving correct GSM menu for Shop page (product archive)
 *
 * What GCW does:
 *  1. GCW adds post_type_support for GSM - see gencwooc_setup()
 *  2. uses Genesis filters to intercept request and serve correct GSM menu on Shop Page
 *
 * Note: this file is loaded on the 'after_theme_setup' hook only if GSM
 * is activated.
 * @see gencwooc_setup() in genesis-connect-woocommerce.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'genesis_pre_get_option_subnav_type', 'gencwooc_gsm_subnav_type', 9 );
/**
 * Tells Genesis to load a custom menu.
 *
 * @since 0.9.0
 *
 * @see Genesis_Simple_Menus::wp_head()
 *
 * @param string $nav The sub nav type.
 *
 * @return string The sub nav type.
 */
function gencwooc_gsm_subnav_type( $nav ) {
	return 'nav-menu';
}


add_filter( 'theme_mod_nav_menu_locations', 'gencwooc_gsm_theme_mod' );
/**
 * Replace the menu selected in the WordPress Menu settings with the custom one for this request.
 *
 * @since 0.9.0
 *
 * @see Genesis_Simple_Menus::wp_head()
 *
 * @param array $mods Array of theme mods.
 *
 * @return array $mods Array of theme mods.
 */
function gencwooc_gsm_theme_mod( $mods ) {

	$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' );
	$_menu   = get_post_meta( $shop_id, '_gsm_menu', true );

	if ( is_post_type_archive( 'product' ) && $_menu ) {
		$mods['secondary'] = (int) $_menu;
	}

	return $mods;

}
