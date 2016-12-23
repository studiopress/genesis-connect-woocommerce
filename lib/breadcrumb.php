<?php
/**
 * This file contains functions related modifying Genesis Breadcrumb output
 *
 * @package genesis_connect_woocommerce
 * @version 0.9.8
 *
 * @since 0.9.0
 *
 *
 * By default, the Genesis Breadcrumb class does not handle Shop pages and taxonomy
 * archives in the same way as WooC's breadcrumbs. These filters and callback
 * functions modify the default Genesis breadcrumb output so that the breadcrumb
 * trail mimics that of WooC's breadcrumbs, for:
 * - Shop page (archive page)
 * - Single product
 * - Taxonomy archive
 *
 * Users who prefer to use WooC's breadcrumbs can do so by adding this to their child
 * theme's functions.php:
 * - add_theme_support( 'gencwooc-woo-breadcrumbs' );
 * And this to the relevant templates:
 * - remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
 *
 * @see readme.txt for more details
 *
 * As this modification code uses existing Genesis Breadcrumb filters there is a risk that
 * it will cause compatibility issues with any existing uses of Genesis Breadcrumb filters.
 * If this is the case, adjusting the filter callback priority in existing filter calls
 * should ensure that each filter callback is called in the correct order.
 *
 * @see genesis/lib/classes/breadcrumb.php v1.8
 * @see woocommerce/templates/shop/breadcrumb.php v1.4.4
 *
 * @TODO Replace with subclass of Genesis_Breadcrumb?
 */


/**
 * Prevent direct access to this file
 */
if ( ! defined( 'ABSPATH' ) )
	exit( __( 'Sorry, you are not allowed to access this file directly.' ) );



add_filter( 'genesis_archive_crumb', 'gencwooc_get_archive_crumb_filter', 10, 2 );
/**
 * Filter the Genesis Breadcrumbs archive crumb
 *
 * Needed for Product Archive (Shop page) and Taxonomy archives
 *
 * Note: relevant WooCommerce settings (WooCommerce > Settings > Pages tab):
 * - woocommerce_prepend_shop_page_to_urls (breadcrumbs and permalinks)
 * - woocommerce_prepend_shop_page_to_products (permalinks only)
 * - woocommerce_prepend_category_to_products (permalinks only)
 *
 * @since 0.9.0
 * @updated 0.9.7
 *
 * @param str $crumb Breadcrumb 'crumb' for archives
 * @param array $args Genesis Breadcrumb args
 * @return str $crumb, either modified $crumb, or original $crumb
 */
function gencwooc_get_archive_crumb_filter( $crumb, $args ) {

	/** Are we on the product archive page? */
	if ( is_post_type_archive( 'product') && get_option( 'page_on_front' ) !== woocommerce_get_page_id( 'shop' ) ) {

		$shop_id = woocommerce_get_page_id( 'shop' );

		$shop_name = $shop_id ? get_the_title( $shop_id ) : ucwords( get_option('woocommerce_shop_slug') );

		if ( is_search() ) :

			$crumb = gencwooc_get_crumb_link( get_post_type_archive_link( 'product' ), $shop_name, $shop_name, $args['sep'] . __( 'Search results for &ldquo;', 'woocommerce' ) . get_search_query() . '&rdquo;' );

		else :

			$crumb = $shop_name;

		endif;

		return apply_filters( 'gencwooc_product_archive_crumb', $crumb, $args );
	}


	/** Are we on a shop taxonomy archive page? */
	if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {

		$crumb = '';

		$prepend = '';

		/** Should we prepend crumb with 'shop' page link? */
		/** See Dashboard > WooC Settings > Pages tab */
		$shop_url = get_option( 'woocommerce_prepend_shop_page_to_urls' );
		$shop_id = woocommerce_get_page_id( 'shop' );
		$shop_title = get_the_title( $shop_id );

		if ( 'yes' == $shop_url && $shop_id && get_option( 'page_on_front' ) !== $shop_id )
			$prepend = gencwooc_get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );

	}

	if ( is_tax( 'product_cat' ) ) {

		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

		$parents = array();
		$parent = $term->parent;
		while ( $parent ):
			$parents[] = $parent;
			$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
			$parent = $new_parent->parent;
		endwhile;

		$crumb .= $prepend;

		if ( ! empty( $parents ) ) :
			$parents = array_reverse( $parents );
			foreach ( $parents as $parent ) :
				$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
				$crumb .= gencwooc_get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
			endforeach;
		endif;

		$crumb .= single_term_title( '', false );

		return $crumb;
	}

	if ( is_tax( 'product_tag' ) ) {

		$crumb .= $prepend . __( 'Products tagged &ldquo;', 'gencwooc' ) . single_term_title( '', false ) . _x( '&rdquo;', 'endquote', 'gencwooc' );

		return $crumb;
	}

	/** Original unmodified */
	return $crumb;
}


add_filter( 'genesis_single_crumb', 'gencwooc_get_single_crumb', 10, 2 );
/**
 * Filter the Genesis Breadcrumbs singular crumb
 *
 * Needed for single Product pages
 *
 * @since 0.9.0
 *
 * @param str $crumb Breadcrumb 'crumb' for single posts
 * @param array $args Genesis Breadcrumb args
 * @return str $crumb, either modified $crumb, or original $crumb
 */
function gencwooc_get_single_crumb( $crumb, $args ) {

	/** Are we on a single product page? */
	if ( is_singular( 'product' ) ) {

		global $post;

		$crumb = '';
		$prepend = '';

		/** Should we prepend crumb with 'shop' page link? */
		/** See Dashboard > WooC Settings > Pages tab */
		$shop_url = get_option( 'woocommerce_prepend_shop_page_to_urls' );
		$shop_id = woocommerce_get_page_id( 'shop' );
		$shop_title = get_the_title( $shop_id );

		if ( 'yes' == $shop_url && $shop_id && get_option( 'page_on_front' ) !== $shop_id )
			$prepend = gencwooc_get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );

		$crumb .= $prepend;

		if ( $terms = wp_get_object_terms( $post->ID, 'product_cat' ) ) :
			$term = current( $terms );
			$parents = array();
			$parent = $term->parent;
			while ( $parent ):
				$parents[] = $parent;
				$new_parent = get_term_by( 'id', $parent, 'product_cat' );
				$parent = $new_parent->parent;
			endwhile;

			if( ! empty( $parents ) ):
				$parents = array_reverse( $parents );
				foreach ( $parents as $parent ) :
					$item = get_term_by( 'id', $parent, 'product_cat' );
					$crumb .= gencwooc_get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
				endforeach;
			endif;
			$crumb .= gencwooc_get_crumb_link( get_term_link( $term->slug, 'product_cat' ), $term->name, $term->name, $args['sep'] );
		endif;

		$crumb .= get_the_title();

		return apply_filters( 'gencwooc_single_product_crumb', $crumb, $args );
	}

	/** Fallback - original unmodified */
	return $crumb;
}


/**
 * Helper function to create anchor link for a single crumb.
 *
 * This is a copy of Genesis_Breadcrumb::get_breadcrumb_link() (G1.8)
 *
 * @since 0.9.0
 *
 * @param string $url URL for href attribute
 * @param string $title title attribute
 * @param string $content linked content
 * @param string $sep Separator
 * @return string HTML markup for anchor link and optional separator.
 */
function gencwooc_get_crumb_link( $url, $title, $content, $sep = false ) {

	$link = sprintf( '<a href="%s" title="%s">%s</a>', esc_attr( $url ), esc_attr( $title ), esc_html( $content ) );

	if ( $sep )
		$link .= $sep;

	return $link;

}