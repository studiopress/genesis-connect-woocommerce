<?php
/**
 * These functions manage loading of plugin-specific addons to the WooCommerce
 * settings page.
 *
 * @package genesis_connect_woocommerce
 * @version 0.9.8
 *
 * @since 0.9.0
 */

/**
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) )
	exit( _( 'Sorry, you are not allowed to access this file directly.' ) );

add_filter( 'woocommerce_settings_tabs_array', 'genesis_connect_addon_tab', 50 );
/*
 * Add a custom tab in the WooCommerce settings for Genesis Connect.
 *
 * @since 1.0.0
 * @return array $tabs Used to add a tab to the WooCommerce settings page.
 */
function genesis_connect_addon_tab( $tabs ) {

	// Add our own section.
	$tabs['gencwooc'] = __( 'Genesis Connect Addons', 'gencwooc' );
	return $tabs;

}

add_action( 'woocommerce_settings_tabs_gencwooc', 'genesis_connect_settings_tab' );
/**
 * Function to add our settings to the new tab.
 *
 * @since 1.0.0
 * @return array Multiple arrays holding our section information
 */

function genesis_connect_settings_tab() {
    woocommerce_admin_fields( genesis_connect_get_settings() );
}

add_action( 'woocommerce_update_options_gencwooc', 'genesis_connect_update_settings' );
/**
 * Update settings.
 *
 * @since 1.0.0
 * @return array Settings to update.
 */
function genesis_connect_update_settings() {
    woocommerce_update_options( genesis_connect_get_settings() );
}

/**
 * Helper function to hold an array of our settings.
 *
 * @since 1.0.0
 * @return array $settings Array of our settings.
 */
function genesis_connect_get_settings() {
    $settings = array(
		'gencwooc_section_title' => array(
			'name' => __( 'Genesis Connect Addons', 'gencwooc' ),
			'type' => 'title',
			'desc' => 'Set and save additional WooCommerce settings here.',
			'id'   => 'gencwooc_section_title'
		),
		'products_per_page' => array(
			'name'    => __( 'Products Per Page', 'gencwooc' ),
			'type'    => 'number',
			'desc'    => __( 'This setting determines how many products show up on archive pages and may be overridden by filters used in themes and plugins.', 'gencwooc' ),
			'id'      => 'gencwooc_products_per_page',
			'default' => apply_filters( 'genesiswooc_default_products_per_page', get_option( 'posts_per_page' ) ),
        ),
        'section_end' => array(
             'type' => 'sectionend',
             'id' => 'gencwooc_section_end'
        )
    );
    return $settings;
}

/**
 * Execute settings on the frontend (this should probably
 * go somewhere else other than this file).
 *
 * @since 1.0.0
 * @return array $q New query for the product page.
 */
add_filter( 'loop_shop_per_page', 'genesiswooc_products_per_page' );
function genesiswooc_products_per_page( $count ) {

	$count = get_option( 'gencwooc_products_per_page' ) ? get_option( 'gencwooc_products_per_page' ) : apply_filters( 'genesiswooc_default_products_per_page', get_option( 'posts_per_page' ) );

	return $count;

}
