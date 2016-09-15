<?php

class Genesis_WooCommerce_Simple_Sidebars {

  public function __construct() {

    add_action( 'get_header', array( $this, 'actions' ) );

  }

  public function actions() {

    /** Unhook GSS functions */
  	remove_action( 'genesis_sidebar', 'ss_do_sidebar' );
  	remove_action( 'genesis_sidebar_alt', 'ss_do_sidebar_alt' );

  	/** Hook replacement functions */
  	add_action( 'genesis_sidebar', array( $this, 'do_sidebar' ) );
  	add_action( 'genesis_sidebar_alt', array( $this, 'do_sidebar_alt' ) );

  }

  public function do_sidebar() {

    $bar = '_ss_sidebar';
  	$shop_id = woocommerce_get_page_id( 'shop' );

  	if ( is_post_type_archive( 'product' ) && $_bar = get_post_meta( $shop_id, $bar, true ) ) {

  		dynamic_sidebar( $_bar );

  	} else {

  		/** Hand back control to GSS */
  		if ( ! ss_do_one_sidebar( $bar ) )
  			genesis_do_sidebar();

  }

  public function do_sidebar_alt() {

  	$bar = '_ss_sidebar_alt';
  	$shop_id = woocommerce_get_page_id( 'shop' );

  	if ( is_post_type_archive( 'product' ) && $_bar = get_post_meta( $shop_id, $bar, true ) ) {
  		dynamic_sidebar( $_bar );

  	} else {

  		/** Hand back control to GSS */
  		if ( ! ss_do_one_sidebar( $bar ) )
  			genesis_do_sidebar_alt();

  	}

  }

}
