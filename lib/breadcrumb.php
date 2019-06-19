<?php
/**
 * This file contains functions related modifying Genesis Breadcrumb output.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 0.9.0
 *
 *
 * By default, the Genesis Breadcrumb class does not handle Shop pages and taxonomy archives in the
 * same way as WooCommerce breadcrumbs. These filters and callback functions modify the default Genesis
 * breadcrumb output so that the breadcrumb trail mimics that of WooCommerce breadcrumbs for:
 * - Shop page (archive page)
 * - Single product
 * - Taxonomy archive
 *
 * Users who prefer to use WooCommerce breadcrumbs can do so by adding this to their child
 * theme's functions.php:
 * - add_theme_support( 'gencwooc-woo-breadcrumbs' );
 * And this to the relevant templates:
 * - remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
 *
 * @see readme.txt For more details.
 *
 * As this modification code uses existing Genesis Breadcrumb filters there is a risk that
 * it will cause compatibility issues with any existing uses of Genesis Breadcrumb filters.
 * If this is the case, adjusting the filter callback priority in existing filter calls
 * should ensure that each filter callback is called in the correct order.
 *
 * @see genesis/lib/classes/breadcrumb.php v1.8
 * @see woocommerce/templates/shop/breadcrumb.php v1.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'genesis_archive_crumb', 'gencwooc_get_archive_crumb_filter', 10, 2 );
/**
 * Filter the Genesis Breadcrumbs archive crumb.
 *
 * Needed for Product Archive (Shop page) and Taxonomy archives.
 *
 * Note: relevant WooCommerce settings (WooCommerce > Settings > Pages tab):
 * - woocommerce_prepend_shop_page_to_urls (breadcrumbs and permalinks)
 * - woocommerce_prepend_shop_page_to_products (permalinks only)
 * - woocommerce_prepend_category_to_products (permalinks only)
 *
 * @since 0.9.0
 *
 * @param string $crumb Breadcrumb 'crumb' for archives.
 * @param array  $args Genesis Breadcrumb args.
 *
 * @return string $crumb Breadcrumb 'crumb' for archives.
 */
function gencwooc_get_archive_crumb_filter( $crumb, $args ) {

	if ( is_post_type_archive( 'product' ) && get_option( 'page_on_front' ) !== wc_get_page_id( 'shop' ) ) {
		$shop_id   = wc_get_page_id( 'shop' );
		$shop_name = $shop_id ? get_the_title( $shop_id ) : ucwords( get_option( 'woocommerce_shop_slug' ) );
		$crumb     = $shop_name;

		if ( is_search() ) {
			$crumb = gencwooc_get_crumb_link(
				get_post_type_archive_link( 'product' ),
				$shop_name,
				$shop_name,
				$args['sep'] . __( 'Search results for &ldquo;', 'gencwooc' ) . get_search_query() . '&rdquo;'
			);
		}

		return apply_filters( 'gencwooc_product_archive_crumb', $crumb, $args );
	}

	if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
		$crumb      = '';
		$prepend    = '';
		$shop_url   = get_option( 'woocommerce_prepend_shop_page_to_urls' );
		$shop_id    = wc_get_page_id( 'shop' );
		$shop_title = get_the_title( $shop_id );

		if ( 'yes' === $shop_url && $shop_id && get_option( 'page_on_front' ) !== $shop_id ) {
			$prepend = gencwooc_get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );
		}
	}

	if ( is_tax( 'product_cat' ) ) {
		$term    = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$parents = array();
		$parent  = $term->parent;

		while ( $parent ) {
			$parents[]  = $parent;
			$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
			$parent     = $new_parent->parent;
		}

		$crumb .= $prepend;

		if ( ! empty( $parents ) ) {
			$parents = array_reverse( $parents );

			foreach ( $parents as $parent ) {
				$item   = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
				$crumb .= gencwooc_get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
			}
		}

		$crumb .= single_term_title( '', false );

		return $crumb;
	}

	if ( is_tax( 'product_tag' ) ) {
		$crumb .= $prepend . __( 'Products tagged &ldquo;', 'gencwooc' ) . single_term_title( '', false ) . _x( '&rdquo;', 'endquote', 'gencwooc' );

		return $crumb;
	}

	return $crumb;

}

add_filter( 'genesis_single_crumb', 'gencwooc_get_single_crumb', 10, 2 );
/**
 * Filter the Genesis Breadcrumbs singular crumb.
 *
 * Needed for single Product pages.
 *
 * @since 0.9.0
 *
 * @global WP_Post $post The current WP_Post.
 *
 * @param string $crumb Breadcrumb 'crumb' for single posts.
 * @param array  $args  Genesis Breadcrumb args.
 *
 * @return string $crumb Breadcrumb 'crumb' for single posts.
 */
function gencwooc_get_single_crumb( $crumb, $args ) {

	if ( is_singular( 'product' ) ) {
		global $post;

		$crumb      = '';
		$prepend    = '';
		$shop_url   = get_option( 'woocommerce_prepend_shop_page_to_urls' );
		$shop_id    = wc_get_page_id( 'shop' );
		$shop_title = get_the_title( $shop_id );

		if ( 'yes' === $shop_url && $shop_id && get_option( 'page_on_front' ) !== $shop_id ) {
			$prepend = gencwooc_get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );
		}

		$crumb .= $prepend;
		$terms  = wp_get_object_terms( $post->ID, 'product_cat' );

		if ( $terms ) {
			$term    = current( $terms );
			$parents = array();
			$parent  = $term->parent;

			while ( $parent ) {
				$parents[]  = $parent;
				$new_parent = get_term_by( 'id', $parent, 'product_cat' );
				$parent     = $new_parent->parent;
			}

			if ( ! empty( $parents ) ) {
				$parents = array_reverse( $parents );

				foreach ( $parents as $parent ) {
					$item   = get_term_by( 'id', $parent, 'product_cat' );
					$crumb .= gencwooc_get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
				}
			}

			$crumb .= gencwooc_get_crumb_link( get_term_link( $term->slug, 'product_cat' ), $term->name, $term->name, $args['sep'] );
		}

		$crumb .= get_the_title();

		return apply_filters( 'gencwooc_single_product_crumb', $crumb, $args );
	}

	return $crumb;

}

/**
 * Helper function to create anchor link for a single crumb.
 *
 * This is a copy of Genesis_Breadcrumb::get_breadcrumb_link() (G1.8).
 *
 * @since 0.9.0
 *
 * @param string $url     URL for href attribute.
 * @param string $title   The title attribute.
 * @param string $content The link content.
 * @param string $sep     Separator. Default false.
 *
 * @return string HTML markup for anchor link and optional separator.
 */
function gencwooc_get_crumb_link( $url, $title, $content, $sep = false ) {

	$link = sprintf(
		'<a href="%s" title="%s">%s</a>',
		esc_attr( $url ),
		esc_attr( $title ),
		esc_html( $content )
	);

	if ( $sep ) {
		$link .= $sep;
	}

	return $link;

}
