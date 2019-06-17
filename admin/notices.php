<?php
/**
 * Callbacks for `admin_notices` action to load HTML notices.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display notice message if WooCommerce is not active.
 *
 * Callback for WordPress 'admin_notices' action.
 *
 * @since 1.0
 */
function gencwooc_woocommerce_notice() {
	include GCW_ADMIN_DIR . '/views/html-notice-needs-woocommerce.php';
}

/**
 * Display notice message if Genesis is not active.
 *
 * Callback for WordPress 'admin_notices' action.
 *
 * @since 1.0
 */
function gencwooc_genesis_notice() {
	include GCW_ADMIN_DIR . '/views/html-notice-needs-genesis.php';
}
