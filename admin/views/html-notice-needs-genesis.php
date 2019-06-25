<?php
/**
 * View for WordPress `admin_notice` if Genesis is not active.
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
		<?php esc_html_e( 'Genesis Connect for WooCommerce requires a Genesis child theme. Please activate a Genesis theme or disable Genesis Connect.', 'gencwooc' ); ?>
	</p>
</div>
