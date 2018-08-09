<?php
/**
 * Callbacks for `admin_notices` action to load HTML notices.
 *
 * @package genesis_connect_woocommerce
 * @version 1.0
 */
defined( 'ABSPATH' ) || exit;

function gencwooc_woocommerce_notice() {
	include GCW_ADMIN_DIR . '/views/html-notice-needs-woocommerce.php';
}

function gencwooc_genesis_notice() {
	include GCW_ADMIN_DIR . '/views/html-notice-needs-genesis.php';
}
