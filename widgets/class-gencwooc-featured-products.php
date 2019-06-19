<?php
/**
 * This file contains a widget to display WooCommerce products in a Genesis - Featured Post format.
 *
 * @package Genesis_Connect_WooCommerce
 * @since 0.9.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Genesis Featured Products widget for the Genesis Connect plugin.
 *
 * @since 1.0.0
 */
class Gencwooc_Featured_Products extends WC_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'set_featured_products' ) );

		$this->defaults = apply_filters(
			'gencwooc_featured_products_defaults',
			array(
				'category'                => '',
				'count'                   => 8,
				'offset'                  => 0,
				'show_type'               => '',
				'orderby'                 => 'date',
				'order'                   => 'desc',
				'hide_free'               => 0,
				'show_hidden'             => 0,
				'show_image'              => 1,
				'image_size'              => 'thumbnail',
				'link_image'              => 1,
				'show_title'              => 1,
				'show_add_to_cart'        => 1,
				'show_price'              => 1,
				'more_from_category'      => 0,
				'more_from_category_text' => __( 'More Products from this Category', 'gencwooc' ),
			)
		);

		$this->widget_cssclass    = 'featured-content featuredproducts';
		$this->widget_name        = __( 'Genesis - Featured Products', 'gencwooc' );
		$this->widget_description = __( 'Displays featured products with thumbnails', 'gencwooc' );
		$this->widget_id          = 'featured-products';
		$this->settings           = array(
			'title'                   => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'gencwooc' ),
			),
			'product_cat'             => array(
				'type'    => 'select',
				'std'     => esc_html( $this->defaults['category'] ),
				'label'   => __( 'Product Category', 'gencwooc' ),
				'options' => array(),
			),
			'product_num'             => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => absint( $this->defaults['count'] ),
				'label' => __( 'Products to Show', 'gencwooc' ),
			),
			'product_offset'          => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 0,
				'max'   => '',
				'std'   => absint( $this->defaults['offset'] ),
				'label' => __( 'Product Offset', 'gencwooc' ),
			),
			'product_show'            => array(
				'type'    => 'select',
				'std'     => esc_html( $this->defaults['show_type'] ),
				'label'   => __( 'Show', 'gencwooc' ),
				'options' => array(
					''         => __( 'All products', 'gencwooc' ),
					'featured' => __( 'Featured products', 'gencwooc' ),
					'onsale'   => __( 'On-sale products', 'gencwooc' ),
				),
			),
			'orderby'                 => array(
				'type'    => 'select',
				'std'     => esc_html( $this->defaults['orderby'] ),
				'label'   => __( 'Order by', 'gencwooc' ),
				'options' => array(
					'date'  => __( 'Date', 'gencwooc' ),
					'price' => __( 'Price', 'gencwooc' ),
					'rand'  => __( 'Random', 'gencwooc' ),
					'sales' => __( 'Sales', 'gencwooc' ),
				),
			),
			'order'                   => array(
				'type'    => 'select',
				'std'     => esc_html( $this->defaults['order'] ),
				'label'   => _x( 'Order', 'Sorting Order', 'gencwooc' ),
				'options' => array(
					'asc'  => __( 'ASC', 'gencwooc' ),
					'desc' => __( 'DESC', 'gencwooc' ),
				),
			),
			'hide_free'               => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['hide_free'] ),
				'label' => __( 'Hide Free Products', 'gencwooc' ),
			),
			'show_hidden'             => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['show_hidden'] ),
				'label' => __( 'Show Hidden Products', 'gencwooc' ),
			),
			'show_image'              => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['show_image'] ),
				'label' => __( 'Show Featured Image?', 'gencwooc' ),
			),
			'image_size'              => array(
				'type'    => 'select',
				'std'     => esc_html( $this->defaults['image_size'] ),
				'label'   => __( 'Image Size', 'gencwooc' ),
				'options' => $this->get_featured_image_sizes(),
			),
			'link_image'              => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['link_image'] ),
				'label' => __( 'Link Product Image?', 'gencwooc' ),
			),
			'show_title'              => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['show_title'] ),
				'label' => __( 'Show Title?', 'gencwooc' ),
			),
			'show_add_to_cart'        => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['show_add_to_cart'] ),
				'label' => __( 'Show Add to Cart Button?', 'gencwooc' ),
			),
			'show_price'              => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['show_price'] ),
				'label' => __( 'Show Price?', 'gencwooc' ),
			),
			'more_from_category'      => array(
				'type'  => 'checkbox',
				'std'   => absint( $this->defaults['more_from_category'] ),
				'label' => __( 'Show Category Archive Link?', 'gencwooc' ),
			),
			'more_from_category_text' => array(
				'type'  => 'text',
				'std'   => esc_html( $this->defaults['more_from_category_text'] ),
				'label' => __( 'Link Text:', 'gencwooc' ),
			),
		);

		parent::__construct();

	}

	/**
	 * Callback for WordPress `admin_init` action.
	 *
	 * Sets the product_cat options using get_featured_product_categories() after the product_cat
	 * taxonomy has been registered.
	 */
	public function set_featured_products() {
		$this->settings['product_cat']['options'] = $this->get_featured_product_categories();
	}

	/**
	 * Function to retrieve the actual product categories as an assosiative array. Used to output
	 * a dropdown in the widget settings.
	 *
	 * @return array Associative array of product categories.
	 */
	public function get_featured_product_categories() {

		$cats    = get_terms( 'product_cat' );
		$options = array(
			'' => __( 'All Categories', 'gencwooc' ),
		);

		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				$options[ $cat->slug ] = $cat->name;
			}
		}

		return $options;

	}

	/**
	 * Function to retrieve an associative array containing all possible featured image sizes to
	 * be used.
	 *
	 * @return array Associative array containg possible image sizes and dimensions.
	 */
	public function get_featured_image_sizes() {

		$options = [];
		$images  = genesis_get_image_sizes();

		foreach ( $images as $size => $data ) {
			$name             = sprintf( '%s: %s x %s', $size, $data['width'], $data['height'] );
			$options[ $size ] = $name;
		}

		return $options;

	}

	/**
	 * Main function to retrieve a WP_Query object with appropriate arguments passed in from
	 * the instance.
	 *
	 * @param array $args     Widgdet instance arguments.
	 * @param array $instance Instance arguments to be used in the query.
	 *
	 * @return object New WP_Query object to be looped through.
	 */
	public function get_featured_products( $args, $instance ) {

		$cat                         = ! empty( $instance['product_cat'] ) ? sanitize_title( $instance['product_cat'] ) : $this->settings['product_cat']['std'];
		$count                       = ! empty( $instance['product_num'] ) ? absint( $instance['product_num'] ) : $this->settings['product_num']['std'];
		$offset                      = ! empty( $instance['product_offset'] ) ? absint( $instance['product_offset'] ) : $this->settings['product_offset']['std'];
		$orderby                     = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
		$order                       = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];
		$product_show                = ! empty( $instance['product_show'] ) ? sanitize_title( $instance['product_show'] ) : $this->settings['product_show']['std'];
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$query_args = array(
			'post_type' => 'product',
			'cat'       => $cat,
			'showposts' => $count,
			'offset'    => $offset,
			'order'     => $order,
			'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'AND',
			),
		);

		switch ( $product_show ) {
			case 'featured':
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
				break;
			case 'onsale':
				$product_ids_on_sale    = wc_get_product_ids_on_sale();
				$product_ids_on_sale[]  = 0;
				$query_args['post__in'] = $product_ids_on_sale;
				break;
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				),
			);
		}

		if ( empty( $instance['show_hidden'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
				'operator' => 'NOT IN',
			);

			$query_args['post_parent'] = 0;
		}

		if ( ! empty( $instance['hide_free'] ) ) {
			// Meta query ensures price and sale price are not explicitly set
			// to a zero-like string. Empty pricing fields do not count as
			// a free product. WooCommerce shows no purchase buttons for those.
			$query_args['meta_query'][] = array(
				'relation' => 'AND',
				array(
					'key'     => '_price',
					'value'   => array( '0', '00', '000', '0000', '0.00', '0,00' ),
					'compare' => 'NOT IN',
				),
				array(
					'key'     => '_sale_price',
					'value'   => array( '0', '00', '000', '0000', '0.00', '0,00' ),
					'compare' => 'NOT IN',
				),
			);
		}

		switch ( $orderby ) {
			case 'price':
				$query_args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			case 'sales':
				$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$query_args['orderby']  = 'meta_value_num';
				break;
			default:
				$query_args['orderby'] = 'date';
		}

		return new WP_Query( apply_filters( 'genwoo_featured_products_widget_query_args', $query_args ) );

	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.9.9
	 *
	 * @global WP_Query $product Product (post) object.
	 *
	 * @param array $args     Display arguments including `before_title`, `after_title`,
	 *                        `before_widget`, and `after_widget`.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$instance = wp_parse_args( $instance, $this->defaults );
		$products = $this->get_featured_products( $args, $instance );

		if ( $products && $products->have_posts() ) {
			$this->widget_start( $args, $instance );

			genesis_markup(
				array(
					'open'    => '<ul %s>',
					'context' => 'featured-products-list',
				)
			);

			while ( $products->have_posts() ) {
				$products->the_post();

				global $product;

				genesis_markup(
					array(
						'open'    => '<li %s>',
						'context' => 'entry-product',
					)
				);

				$image = genesis_get_image(
					array(
						'format'  => 'html',
						'size'    => $instance['image_size'],
						'context' => 'featured-product-image',
						'attr'    => genesis_parse_attr( 'featured-product-image', array( 'alt' => get_the_title() ) ),
					)
				);

				if ( $image && $instance['show_image'] ) {
					if ( $instance['link_image'] ) {
						printf(
							'<a href="%s" class="entry-image-wrap">%s</a>',
							esc_url( get_permalink() ),
							wp_make_content_images_responsive( $image ) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					} else {
						printf(
							'<div class="entry-image-wrap">%s</div>',
							wp_make_content_images_responsive( $image ) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				}

				if ( $instance['show_title'] ) {
					$header = '';

					if ( ! empty( $instance['show_title'] ) ) {
						$title = get_the_title() ? get_the_title() : __( '(no title)', 'gencwooc' );

						/**
						 * Filter the featured post widget title.
						 *
						 * @since  2.2.0
						 *
						 * @param string $title    Featured post title.
						 * @param array  $instance {
						 *     Widget settings for this instance.
						 *
						 *     @type string $title                   Widget title.
						 *     @type int    $product_cat               ID of the post category.
						 *     @type int    $product_num               Number of posts to show.
						 *     @type int    $product_offset            Number of posts to skip when
						 *                                           retrieving.
						 *     @type string $orderby                 Field to order posts by.
						 *     @type string $order                   ASC fr ascending order, DESC for
						 *                                           descending order of posts.
						 *     @type bool   $show_image              True if featured image should be
						 *                                           shown, false otherwise.
						 *     @type bool   $show_hidden             True if hidden products should be
						 *                                           shown, false otherwise.
						 *     @type string $image_size              Name of the image size.
						 *     @type bool   $show_title              True if featured page title should
						 *                                           be shown, false otherwise.
						 *     @type int    $extra_num               Number of extra post titles to show.
						 *     @type string $extra_title             Heading for extra posts.
						 *     @type bool   $more_from_category      True if showing category archive
						 *                                           link, false otherwise.
						 *     @type string $more_from_category_text Category archive link text.
						 * }
						 * @param array  $args     {
						 *     Widget display arguments.
						 *
						 *     @type string $before_widget Markup or content to display before the widget.
						 *     @type string $before_title  Markup or content to display before the widget title.
						 *     @type string $after_title   Markup or content to display after the widget title.
						 *     @type string $after_widget  Markup or content to display after the widget.
						 * }
						 */
						$title   = apply_filters( 'genesis_featured_product_title', $title, $instance, $args );
						$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';

						$header .= genesis_markup(
							array(
								'open'    => "<{$heading} %s>",
								'close'   => "</{$heading}>",
								'context' => 'entry-product-title',
								'content' => sprintf( '<a href="%s">%s</a>', get_permalink(), $title ),
								'echo'    => false,
							)
						);

					}

					genesis_markup(
						array(
							'open'    => '<header %s>',
							'close'   => '</header>',
							'context' => 'entry-product-header',
							'content' => $header,
						)
					);

				}

				if ( $instance['show_price'] && $product->get_price_html() ) {
					printf( '<span class="price">%s</span>', $product->get_price_html() ); // phpcs:ignore WordPress.Security.EscapeOutput
				}

				if ( $instance['show_add_to_cart'] ) {
					woocommerce_template_loop_add_to_cart( $product->get_id() );
				}

				genesis_markup(
					array(
						'close'   => '</li>',
						'context' => 'entry-product',
					)
				);
			}

			genesis_markup(
				array(
					'close'   => '</ul>',
					'context' => 'featured-products-list',
				)
			);

			if ( ! empty( $instance['more_from_category'] ) ) {
				$cat = get_term_by( 'name', $instance['product_cat'], 'product_cat' );

				printf(
					'<p class="more-from-category"><a href="%1$s" title="%2$s">%3$s</a></p>',
					esc_url( get_term_link( $cat->term_taxonomy_id ) ),
					esc_attr( $cat->name ),
					esc_html( $instance['more_from_category_text'] )
				);
			}

			$this->widget_end( $args );

		}

		wp_reset_postdata();

		echo $this->cache_widget( $args, ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput

	}

}
