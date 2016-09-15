<?php
/*
Plugin Name: Genesis WooCommerce
*/

class Genesis_WooCommerce() {

	public $plugin_dir_path;

	public $plugin_dir_url;

	public $breadcrumbs;

	public $templates;

	public $simple_sidebars;

	public $simple_menus;

	public function __construct() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );

		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'after_setup_theme', array( $this, 'add_post_type_support' ) );
		add_action( 'plugins_loaded', array( $this, 'set_up_objects' ) );

	}

	public function add_theme_support() {

		add_theme_support( 'woocommerce' );

	}

	public function add_post_type_support() {

		add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-seo' ) );
		add_post_type_support( 'product', array( 'genesis-simple-sidebars', 'genesis-simple-menus' ) );

	}

	public function set_up_objects() {

		require_once( $this->plugin_dir_path . 'includes/class-genesis-woocommerce-breadcrumbs.php' );
		$this->breadcrumbs = new Genesis_WooCommerce_Breadcrumbs;

		require_once( $this->plugin_dir_path . 'includes/class-genesis-woocommerce-templates.php' );
		$this->templates = new Genesis_WooCommerce_Templates;

		//require_once( $this->plugin_dir_path . 'includes/class-genesis-woocommerce-simple-sidebars.php' );
		//$this->simple_sidebars = new Genesis_WooCommerce_Simple_Sidebars;

		//require_once( $this->plugin_dir_path . 'includes/class-genesis-woocommerce-simple-menus.php' );
		//$this->simple_menus = new Genesis_WooCommerce_Simple_Menus;

	}

}

function Genesis_WooCommerce() {

	static $_genesis_woocommerce = null;

	if ( null == $_genesis_woocommerce ) {
		$_genesis_woocommerce = new Genesis_WooCommerce;
	}

	return $_genesis_woocommerce;

}

Genesis_WooCommerce();
