<?php

class Genesis_WooCommerce_Breadcrumbs {

  public function __construct() {

    $this->filters();

  }

  public function filters() {

    add_filter( 'genesis_archive_crumb', array( $this, 'get_archive_crumb' ), 10, 2 );
    add_filter( 'genesis_single_crumb', array( $this, 'get_single_crumb' ), 10, 2 );

  }

  public function get_crumb_link( $url, $content, $title, $sep = false ) {

    $link = sprintf( '<a href="%s" title="%s">%s</a>', esc_attr( $url ), esc_attr( $title ), esc_html( $content ) );

  	if ( $sep ) {
  		$link .= $sep;
    }

  	return $link;

  }

  function get_archive_crumb( $crumb, $args ) {

    /** Are we on the product archive page? */
  	if ( is_post_type_archive( 'product') && get_option( 'page_on_front' ) !== woocommerce_get_page_id( 'shop' ) ) {

  		$shop_id = woocommerce_get_page_id( 'shop' );

  		$shop_name = $shop_id ? get_the_title( $shop_id ) : ucwords( get_option('woocommerce_shop_slug') );

  		if ( is_search() ) :

  			$crumb = $this->get_crumb_link( get_post_type_archive_link( 'product' ), $shop_name, $shop_name, $args['sep'] . __( 'Search results for &ldquo;', 'woocommerce' ) . get_search_query() . '&rdquo;' );

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
  			$prepend = $this->get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );

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
  				$crumb .= $this->get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
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

  function get_single_crumb( $crumb, $args ) {

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
  			$prepend = $this->get_crumb_link( get_permalink( $shop_id ), $shop_title, $shop_title, $args['sep'] );

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
  					$crumb .= $this->get_crumb_link( get_term_link( $item->slug, 'product_cat' ), $item->name, $item->name, $args['sep'] );
  				endforeach;
  			endif;
  			$crumb .= $this->get_crumb_link( get_term_link( $term->slug, 'product_cat' ), $term->name, $term->name, $args['sep'] );
  		endif;

  		$crumb .= get_the_title();

  		return apply_filters( 'gencwooc_single_product_crumb', $crumb, $args );
  	}

  	/** Fallback - original unmodified */
  	return $crumb;

  }

}
