<?php

class Genesis_WooCommerce_Templates {

  public $template_dir;

  public function __construct() {

    $this->template_dir = Genesis_WooCommerce()->plugin_dir_path;

    $this->filters();

  }

  protected function filters() {

    global $woocommerce;

    remove_filter( 'template_include', array( $woocommerce, 'template_loader' ) );
    add_filter( 'template_include', array( $this, 'template_loader' ), 20 );

  }

  public function template_loader( $template ) {

    if ( is_single() && 'product' == get_post_type() ) {

  		$template = locate_template( array( 'woocommerce/single-product.php' ) );

  		if ( ! $template )
  			$template = $this->template_dir . 'single-product.php';

  	}
  	elseif ( is_post_type_archive( 'product' ) ||  is_page( get_option( 'woocommerce_shop_page_id' ) ) ) {

  		$template = locate_template( array( 'woocommerce/archive-product.php' ) );

  		if ( ! $template )
  			$template = $this->template_dir . 'archive-product.php';

  	}
  	elseif ( is_tax() ) {

  		$term = get_query_var( 'term' );

  		$tax = get_query_var( 'taxonomy' );

  		/** Get an array of all relevant taxonomies */
  		$taxonomies = get_object_taxonomies( 'product', 'names' );

  		if ( in_array( $tax, $taxonomies ) ) {

  			$tax = sanitize_title( $tax );
  			$term = sanitize_title( $term );

  			$templates = array(
  				'woocommerce/taxonomy-'.$tax.'-'.$term.'.php',
  				'woocommerce/taxonomy-'.$tax.'.php',
  				'woocommerce/taxonomy.php',
  			);

  			$template = locate_template( $templates );

  			/** Fallback to GCW template */
  			if ( ! $template )
  				$template = $this->template_dir . 'taxonomy.php';
  		}
  	}

  	return $template;

  }

  public function woo_content() {

    do_action('woocommerce_before_main_content');

    echo '<h1 class="page-title">';
    if ( is_search() ) {

        printf( __( 'Search Results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );

        if ( get_query_var( 'paged' ) ) {
    			printf( __( '&nbsp;&ndash; Page %s', 'woocommerce' ), get_query_var( 'paged' ) );
        }
        elseif ( is_tax() ) {
          echo single_term_title( "", false );
        }

    } else {

    		$shop_page = get_post( woocommerce_get_page_id( 'shop' ) );

        echo apply_filters( 'the_title', ( $shop_page_title = get_option( 'woocommerce_shop_page_title' ) ) ? $shop_page_title : $shop_page->post_title );

    }
    echo '</h1>';

    if ( is_tax() && get_query_var( 'paged' ) == 0 ) {

      echo '<div class="term-description">' . wpautop( wptexturize( term_description() ) ) . '</div>';

    } else {

      if ( ! is_search() && get_query_var( 'paged' ) == 0 && ! empty( $shop_page ) && is_object( $shop_page ) ) {
        echo '<div class="page-description">' . apply_filters( 'the_content', $shop_page->post_content ) . '</div>';
      }

    }

    if ( have_posts() ) :

      do_action('woocommerce_before_shop_loop');

    	echo '<ul class="products">';
    			woocommerce_product_subcategories();

      while ( have_posts() ) : the_post();
    	woocommerce_get_template_part( 'content', 'product' );
    	endwhile; // end of the loop.

    	echo '</ul>';

      do_action('woocommerce_after_shop_loop');

    else :

      if ( ! woocommerce_product_subcategories( array( 'before' => '<ul class="products">', 'after' => '</ul>' ) ) ) {
          echo '<p>' . __( 'No products found which match your selection.', 'woocommerce' ) . '</p>';
    	}

    endif;

    echo '<div class="clear"></div>';

    do_action( 'woocommerce_pagination' );

    do_action('woocommerce_after_main_content');

  }

}
