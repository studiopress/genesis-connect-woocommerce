<?php
/**
 * View for WordPress `admin_notice` if WooCommerce is not active.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="error notice">
	<p>
		<?php esc_html_e( 'Genesis Connect for WooCommerce requires WooCommerce. Please activate WooCommerce or disable Genesis Connect.', 'gencwooc' ); ?>
	</p>
</div>
