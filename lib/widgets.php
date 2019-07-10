<?php
/**
 * Widget callback functions.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'widgets_init', 'gencwooc_register_featured_products_widget' );
/**
 * Register Gencwooc_Featured_Products widget.
 *
 * Callback for WordPress 'widgets_init' action.
 *
 * @since 1.0.0
 */
function gencwooc_register_featured_products_widget() {

	if ( class_exists( 'Gencwooc_Featured_Products' ) ) {
		register_widget( 'Gencwooc_Featured_Products' );
	}

}
