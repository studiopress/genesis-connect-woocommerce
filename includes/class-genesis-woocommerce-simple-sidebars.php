<?php

class Genesis_WooCommerce_Simple_Menus {

  public function __construct() {

    $this->filters();
    $this->actions();

  }

  public function filters() {

    add_filter( 'genesis_pre_get_option_subnav_type', array( $this, 'subnav_type' ), 9 );
    add_filter( 'theme_mod_nav_menu_locations', array( $this, 'theme_mods' ) );

  }

  public function subnav_type( $nav ) {
    return 'nav-menu';
  }

  public function theme_mods( $mods ) {

    /** Post meta key as per GSM 0.1.4 */
    $field_name = '_gsm_menu';

    $shop_id = woocommerce_get_page_id( 'shop' );

    if ( is_post_type_archive( 'product' ) && $_menu = get_post_meta( $shop_id, $field_name, true ) )
      $mods['secondary'] = (int) $_menu;

    return $mods;

  }

}
