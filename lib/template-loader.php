<?php
/**
 * These functions manage loading of templates for WooCommerce.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 0.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the Genesis-fied templates, instead of the WooCommerce defaults.
 *
 * Hooked to 'template_include' filter.
 *
 * This template loader determines which template file will be used for the requested page,
 * and uses the following hierarchy to find the template:
 * 1. First looks in the child theme's 'woocommerce' folder.
 * 2. If no template found, falls back to GCW's templates.
 *
 * For taxonomy templates, first looks in child theme's 'woocommerce' folder and searches for term
 * specific template, then taxonomy specific template, then taxonomy.php. If no template found,
 * falls back to GCW's taxonomy.php.
 *
 * GCW provides three templates in the plugin's 'templates' directory:
 * - single-product.php
 * - archive-product.php
 * - taxonomy.php
 *
 * Users can override GCW templates by placing their own templates in their child theme's
 * 'woocommerce' folder. The 'woocommerce' folder must be a folder in the child theme's root
 * directory, eg themes/my-child-theme/woocommerce.
 *
 * Permitted user templates (as per WP Template Hierarchy) are:
 * - single-product.php
 * - archive-product.php
 * - taxonomy-{taxonomy-name}-{term-name}.php
 * - taxonomy-{taxonomy-name}.php
 * - taxonomy.php
 *
 * Note that in the case of taxonomy templates, this function accommodates ALL taxonomies
 * registered to the 'product' custom post type. This means that it will cater for users' own
 * custom taxonomies as well as WooCommerce taxonomies.
 *
 * @since 0.9.0
 *
 * @param string $template Template file as per template hierarchy.
 *
 * @return string $template Template file as per template hierarchy.
 */
function gencwooc_template_loader( $template ) {

	if ( class_exists( 'WC_Embed' ) && WC_Embed::is_embedded_product() ) {
		return $template;
	}

	if ( is_single() && 'product' === get_post_type() ) {
		$template = locate_template( array( 'woocommerce/single-product.php' ) );

		if ( ! $template ) {
			$template = GCW_TEMPLATE_DIR . '/single-product.php';
		}
	} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
		$template = locate_template( array( 'woocommerce/archive-product.php' ) );

		if ( ! $template ) {
			$template = GCW_TEMPLATE_DIR . '/archive-product.php';
		}
	} elseif ( is_tax() ) {
		$term       = get_query_var( 'term' );
		$tax        = get_query_var( 'taxonomy' );
		$taxonomies = get_object_taxonomies( 'product', 'names' );

		if ( in_array( $tax, $taxonomies, true ) ) {
			$tax  = sanitize_title( $tax );
			$term = sanitize_title( $term );

			$templates = array(
				"woocommerce/taxonomy-{$tax}-{$term}.php",
				"woocommerce/taxonomy-{$tax}.php",
				'woocommerce/taxonomy.php',
			);

			$template = locate_template( $templates );

			if ( ! $template ) {
				$template = GCW_TEMPLATE_DIR . '/taxonomy.php';
			}
		}
	}

	return $template;

}

/**
 * Shop Loop 'template part' loader.
 *
 * ONLY RETAINED FOR BACKWARDS COMPATIBILITY for GCW pre-0.9.2 custom templates which
 * may use this function.
 *
 * Function looks for loop-shop.php in child theme's 'woocommerce' folder. If it doesn't exist,
 * loads the default WooCommerce loop-shop.php file.
 *
 * Note: loop-shop.php is used to display products on the archive and taxonomy pages.
 *
 * Users can override the default WooCommerce loop-shop.php by placing their own template
 * (named loop-shop.php) in their child theme's 'woocommerce' folder. The'woocommerce' folder
 * must be a folder in the child theme root directory, eg themes/my-child-theme/woocommerce.
 *
 * It is recommended to use woocommerce/templates/loop-shop.php as the starting point of
 * any custom loop template.
 *
 * Based on woocommerce_get_template_part()
 *
 * Note: updated v0.9.3 to reflect changes to woocommerce_get_template_part() introduced in
 * WooCommerce v1.4+ and, effectively, this function is a clone of woocommerce_get_template_part()
 *
 * @global object $woocommerce WooCommerce instance
 *
 * @since 0.9.0
 *
 * @param string $slug The template slug.
 * @param string $name The template name.
 */
function gencwooc_get_template_part( $slug, $name = '' ) {

	global $woocommerce;

	$template = '';

	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", "{$woocommerce->template_url}{$slug}-{$name}.php" ) );
	}

	if ( ! $template && $name && file_exists( $woocommerce->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = $woocommerce->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", "{$woocommerce->template_url}{$slug}.php" ) );
	}

	if ( $template ) {
		load_template( $template, false );
	}

}

/**
 * Display shop items.
 *
 * For backwards compatibility with WooCommerce versions pre-1.6.0.
 *
 * Uses WooCommerce structure and contains all existing WooCommerce hooks.
 *
 * Note that this will also display any content created in the Shop Page itself.
 *
 * Code based on WooCommerce 1.5.5 woocommerce_archive_product_content().
 *
 * @see woocommerce/woocommerce-template.php
 *
 * @since 0.9.4
 *
 * @global string|int $shop_page_id The ID of the Shop WP Page.
 */
function genesiswooc_product_archive() {

	global $shop_page_id;

	if ( ! is_search() ) {
		$shop_page         = get_post( $shop_page_id );
		$shop_page_title   = apply_filters( 'the_title', ( get_option( 'woocommerce_shop_page_title' ) ) ? get_option( 'woocommerce_shop_page_title' ) : $shop_page->post_title, $shop_page->ID );
		$shop_page_content = $shop_page->post_content;
	} else {
		$shop_page_title = __( 'Search Results:', 'gencwooc' ) . ' &ldquo;' . get_search_query() . '&rdquo;';

		if ( get_query_var( 'paged' ) ) {
			$shop_page_title .= ' &mdash; ' . __( 'Page', 'gencwooc' ) . ' ' . get_query_var( 'paged' );
		}

		$shop_page_content = '';
	}

	do_action( 'woocommerce_before_main_content' );
	?>

	<h1 class="page-title"><?php echo $shop_page_title; // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>

	<?php

	echo apply_filters( 'the_content', $shop_page_content ); // phpcs:ignore WordPress.Security.EscapeOutput

	woocommerce_get_template_part( 'loop', 'shop' );

	do_action( 'woocommerce_pagination' );

	do_action( 'woocommerce_after_main_content' );

}

/**
 * Displays shop items for the queried taxonomy term.
 *
 * For backwards compatibility with WooCommerce versions pre-1.6.0.
 *
 * Uses WooCommerce structure and contains all existing WooCommerce hooks.
 *
 * Code based on WooCommerce 1.5.5 woocommerce_product_taxonomy_content().
 *
 * @see woocommerce/woocommerce-template.php
 *
 * @since 0.9.4
 */
function genesiswooc_product_taxonomy() {

	do_action( 'woocommerce_before_main_content' );

	woocommerce_get_template_part( 'loop', 'shop' );

	do_action( 'woocommerce_pagination' );

	do_action( 'woocommerce_after_main_content' );

}

/**
 * Displays shop items for archives (taxonomy and main shop page).
 *
 * Only loaded if WooCommerce 1.6.0+ is in use.
 *
 * Uses WooCommerce structure and contains all existing WooCommerce hooks.
 *
 * Code based on WooCommerce 2.1.12 templates/archive-product.php.
 *
 * @since 0.9.4
 */
function genesiswooc_content_product() {

	/**
	 * For woocommerce_before_main_content hook.
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action( 'woocommerce_before_main_content' );

	if ( apply_filters( 'woocommerce_show_page_title', true ) ) {
		echo '<h1 class="woocommerce-products-header__title page-title">';

		woocommerce_page_title();

		echo '</h1>';
	}

	do_action( 'woocommerce_archive_description' );

	if ( have_posts() ) {
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked wc_print_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );

		woocommerce_product_loop_start();

		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 *
				 * @hooked WC_Structured_Data::generate_product_data() - 10
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
			}
		}

		woocommerce_product_loop_end();

		/**
		 * Hook: woocommerce_after_shop_loop.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
	} else {
		/**
		 * Hook: woocommerce_no_products_found.
		 *
		 * @hooked wc_no_products_found - 10
		 */
		do_action( 'woocommerce_no_products_found' );
	}

	/**
	 * Hook: woocommerce_after_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );

}
