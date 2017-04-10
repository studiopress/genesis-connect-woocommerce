<?php
/**
 * This file contains a widget to display WooCommerce products in a Genesis - Featured Post format.
 *
 * @package genesis_connect_woocommerce
 * @version 0.9.9
 *
 * @since 0.9.9
 */

 /**
  * Genesis Featured Products widget for the Genesis Connect plugin.
  *
  * @since 1.0.0
  *
  * @package Canvas
  */
 class Genwooc_Featured_Products extends WC_Widget {

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

        // Retrieve a list of product categories.
 		add_action( 'admin_init',                        array( $this, 'set_featured_products'        ) );

        // Set the widget default settings.
        $this->defaults = apply_filters( 'genwooc_featured_products_defaults', array(
			'category'                => '',
			'count'                   => 8,
			'offset'                  => 0,
			'show_type'               => '',
			'columns'                 => '',
			'orderby'                 => 'date',
			'order'                   => 'desc',
			'show_free'               => 0,
			'show_hidden'             => 0,
			'show_image'              => 1,
			'image_size'              => 'thumbnail',
			'link_image'              => 1,
			'image_alignment'         => '',
			'show_title'              => 1,
			'show_add_to_cart'        => 1,
			'show_price'              => 1,
			'more_from_category'      => 0,
			'more_from_category_text' => __( 'More Products from this Category', 'genwooc' ),
		));

 		$this->widget_cssclass    = 'featured-content featuredproducts';
 		$this->widget_name        = __( 'Genesis - Featured Products', 'genwooc' );
 		$this->widget_description = __( 'Displays featured products with thumbnails', 'genwooc' );
 		$this->widget_id          = 'featured-products';
 		$this->settings           = array(
 			'title'    => array(
 				'type'  => 'text',
 				'std'   => '',
 				'label' => __( 'Title', 'genwooc' ),
 			),
 			'product_cat' => array(
 				'type'   => 'select',
 				'std'    => esc_html( $this->defaults['category'] ),
 				'label'  => __( 'Product Category', 'genwooc' ),
 				'options' => array(),
 			),
 			'product_num' => array(
 				'type' => 'number',
 				'step' => 1,
 				'min'  => 1,
 				'max'  => '',
 				'std'  => absint( $this->defaults['count'] ),
 				'label' => __( 'Products to Show', 'genwooc' ),
 			),
 			'product_offset' => array(
 				'type' => 'number',
 				'step' => 1,
 				'min'  => 0,
 				'max'  => '',
 				'std'  => absint( $this->defaults['offset'] ),
 				'label' => __( 'Product Offset', 'genwooc' ),
 			),
 			'product_show' => array(
 				'type' => 'select',
 				'std'  => esc_html( $this->defaults['show_type'] ),
 				'label' => __( 'Show', 'genwooc' ),
 				'options' => array(
                    ''         => __( 'All products', 'genwooc' ),
                    'featured' => __( 'Featured products', 'genwooc' ),
                    'onsale'   => __( 'On-sale products', 'genwooc' ),
                 ),
 			),
			'columns' => array(
				'type' => 'select',
				'std'  => esc_html( $this->defaults['columns'] ),
				'label' => __( 'Product Columns', 'genwooc' ),
				'options' => array(
					'' => __( 'None', 'genwooc' ),
					'column-halves' => __( 'One-Half', 'genwooc' ),
					'column-thirds' => __( 'One-Third', 'genwooc' ),
					'column-fourths' => __( 'One-Fourth', 'genwooc' ),
					'column-fifths' => __( 'One-Fifth', 'genwooc' ),
					'column-sixths' => __( 'One-Sixth', 'genwooc' ),
				),
			),
 			'orderby' => array(
                 'type'  => 'select',
                 'std'   => esc_html( $this->defaults['orderby'] ),
                 'label' => __( 'Order by', 'genwooc' ),
                 'options' => array(
                     'date'   => __( 'Date', 'genwooc' ),
                     'price'  => __( 'Price', 'genwooc' ),
                     'rand'   => __( 'Random', 'genwooc' ),
                     'sales'  => __( 'Sales', 'genwooc' ),
                 ),
             ),
 			'order' => array(
                 'type'  => 'select',
                 'std'   => esc_html( $this->defaults['order'] ),
                 'label' => _x( 'Order', 'Sorting Order', 'genwooc' ),
                 'options' => array(
                     'asc'  => __( 'ASC', 'genwooc' ),
                     'desc' => __( 'DESC', 'genwooc' ),
                 ),
             ),
 			'hide_free' => array(
                 'type'  => 'checkbox',
                 'std'   => absint( $this->defaults['show_free'] ),
                 'label' => __( 'Hide Free Products', 'genwooc' ),
             ),
 			'show_hidden' => array(
                 'type'  => 'checkbox',
                 'std'   => absint( $this->defaults['show_hidden'] ),
                 'label' => __( 'Show Hidden Products', 'gensis' ),
             ),
 			'show_image' => array(
 				'type'  => 'checkbox',
 				'std'   => absint( $this->defaults['show_image'] ),
 				'label' => __( 'Show Featured Image?', 'genwooc' ),
 			),
			'image_size' => array(
 				'type' => 'select',
 				'std'  => esc_html( $this->defaults['image_size'] ),
 				'label' => __( 'Image Size', 'genwooc' ),
 				'options' => $this->get_featured_image_sizes(),
 			),
			'link_image' => array(
				'type' => 'checkbox',
				'std'  => absint( $this->defaults['link_image'] ),
				'label' => __( 'Link Product Image?', 'genwooc' ),
			),
 			'image_alignment' => array(
 				'type' => 'select',
 				'std'  => esc_html( $this->defaults['image_alignment'] ),
 				'label' => __( 'Image Alignment', 'genwooc' ),
 				'options' => array(
 					''            => __( 'None', 'genwooc' ),
 					'alignright'  => __( 'Align Right', 'genwooc' ),
 					'alignleft'   => __( 'Align Left', 'genwooc' ),
 					'aligncenter' => __( 'Align Center', 'genwooc' ),
 				),
 			),
 			'show_title' => array(
 				'type' => 'checkbox',
 				'std'  => absint( $this->defaults['show_title'] ),
 				'label' => __( 'Show Title?', 'genwooc' ),
 			),
			'show_add_to_cart' => array(
				'type' => 'checkbox',
				'std'  => absint( $this->defaults['show_add_to_cart'] ),
				'label' => __( 'Show Add to Cart Button?', 'genwooc' ),
			),
			'show_price' => array(
				'type' => 'checkbox',
				'std'  => absint( $this->defaults['show_price'] ),
				'label' => __( 'Show Price?', 'genwooc' ),
			),
 			'more_from_category' => array(
 				'type' => 'checkbox',
 				'std'  => absint( $this->defaults['more_from_category'] ),
 				'label' => __( 'Show Category Archive Link?', 'genwooc' ),
 			),
 			'more_from_category_text' => array(
 				'type' => 'text',
				'std'  => esc_html( $this->defaults['more_from_category_text'] ),
 				'label' => __( 'Link Text:', 'genwooc' ),
 			),
 		);

 		parent::__construct();

 	}

 	/**
 	 * Callback on the `admin_init` action.
 	 * Sets the product_cat options using get_featured_product_categories()
 	 * after the product_cat taxonomy has been registered.
 	 *
 	 * @return void
 	 */
 	public function set_featured_products() {

 		$this->settings['product_cat']['options'] = $this->get_featured_product_categories();

 	}

    /**
     * Function to retrieve the actual product categories as an assosiative
     * array. Used to output a dropdown in the widget settings.
     *
     * @return array Associative array of product categories.
     */
 	public function get_featured_product_categories() {

 		$cats = get_terms( 'product_cat' );
 		$options = array(
 			'' => __( 'All Categories', 'genwooc' ),
 		);

 		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
 			foreach( $cats as $cat ) {
 				$options[$cat->slug] = $cat->name;
 			}
 		}

 		return $options;

 	}

    /**
     * Function to retrieve an associative array containing all possible
     * featured image sizes to be used.
     *
     * @return array Associative array containg possible image sizes and dimensions.
     */
 	public function get_featured_image_sizes() {

 		$options = [];
 		$images = genesis_get_image_sizes();

 		foreach( $images as $size => $data ) {
 			$name = sprintf( '%s: %s x %s', $size, $data['width'], $data['height'] );
 			$options[$size] = $name;
 		}

 		return $options;

 	}

    /**
     * Main function to retrieve a WP_Query object with appropriate arguments passed in from the instance.
     *
     * @param  array  $instance Instance arguments to be used in the query.
     * @return object           New WP_Query object to be looped through.
     */
 	public function get_featured_products( $instance ) {
 		$cat     = ! empty( $instance['product_cat'] )    ? sanitize_title( $instance['product_cat'] ) : $this->settings['product_cat']['std'];
 		$count   = ! empty( $instance['product_num'] )    ? absint( $instance['product_num'] )         : $this->settings['product_num']['std'];
 		$offset  = ! empty( $instance['product_offset'] ) ? absint( $instance['product_offset'] )      : $this->settings['product_offset']['std'];

 		$orderby                     = ! empty( $instance['orderby'] )      ? sanitize_title( $instance['orderby'] )      : $this->settings['orderby']['std'];
 	   	$order                       = ! empty( $instance['order'] )        ? sanitize_title( $instance['order'] )        : $this->settings['order']['std'];
 		$product_show                = ! empty( $instance['product_show'] ) ? sanitize_title( $instance['product_show'] ) : $this->settings['product_show']['std'];
 	   	$product_visibility_term_ids = wc_get_product_visibility_term_ids();

 		$query_args = array(
 			'post_type' => 'product',
 			'cat'       => $cat,
 			'showposts' => $count,
 			'offset'    => $offset,
 			'order'     => $order,
            'tax_query' => array(
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
            $query_args['tax_query'] = array(
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

 		switch ( $orderby ) {
            case 'price':
                $query_args['meta_key'] = '_price';
                $query_args['orderby']  = 'meta_value_num';
                break;
            case 'rand':
                $query_args['orderby']  = 'rand';
                break;
            case 'sales':
                $query_args['meta_key'] = 'total_sales';
                $query_args['orderby']  = 'meta_value_num';
                break;
            default:
                $query_args['orderby']  = 'date';
        }

 		return new WP_Query( apply_filters( 'genwoo_featured_products_widget_query_args', $query_args ) );
 	}

 	/**
 	 * Echo the widget content.
 	 *
 	 * @since 0.1.8
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

		$this->widget_start( $args, $instance );

		if ( isset( $instance['columns'] ) && ! empty( $instance['columns'] ) ) {
			printf( '<div class="%s">', $instance['columns'] );
		}

		$products = $this->get_featured_products( $instance );

 		if ( $products && $products->have_posts() ) {
 			while ( $products->have_posts() ) {
 				$products->the_post();
 				global $product;

 				genesis_markup( array(
 					'open'    => '<article %s>',
 					'context' => 'entry-widget',
 				) );

 				$image = genesis_get_image( array(
 					'format'  => 'html',
 					'size'    => $instance['image_size'],
 					'context' => 'featured-product-widget',
 					'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => get_the_title() ) ),
 				) );

 				if ( $image && $instance['show_image'] ) {
 					$role = empty( $instance['show_title'] ) ? '' : 'aria-hidden="true"';
					if ( $instance['link_image'] ) {
						printf( '<a href="%s" class="%s" %s>%s</a>', get_permalink(), esc_attr( $instance['image_alignment'] ), $role, wp_make_content_images_responsive( $image ) );
					} else {
						printf( '<div class="%s" %s>%s</div>', esc_attr( $instance['image_alignment'] ), $role, wp_make_content_images_responsive( $image ) );
					}
 				}

 				if ( $instance['show_title'] ) {

 					$header = '';

 					if ( ! empty( $instance['show_title'] ) ) {

 						$title = get_the_title() ? get_the_title() : __( '(no title)', 'genwooc' );

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
 						 *     @type string $image_alignment         Image alignment: `alignnone`,
 						 *                                           `alignleft`, `aligncenter` or `alignright`.
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
 						$title = apply_filters( 'genesis_featured_product_title', $title, $instance, $args );
 						$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';

 						$header .= genesis_markup( array(
 							'open'    => "<{$heading} %s>",
 							'close'   => "</{$heading}>",
 							'context' => 'entry-title-widget',
 							'content' => sprintf( '<a href="%s">%s</a>', get_permalink(), $title ),
 							'echo'    => false,
 						) );

 					}

 					genesis_markup( array(
 						'open'    => '<header %s>',
 						'close'   => '</header>',
 						'context' => 'entry-header-widget',
 						'content' => $header,
 					) );

 				}

				if ( $instance['show_add_to_cart'] ) {
					woocommerce_template_loop_add_to_cart( $product->ID );
				}

				if ( $instance['show_price'] && $product->get_price_html() ) {
					printf( '<span class="price">%s</span>', $product->get_price_html() );
				}

				genesis_markup( array(
 					'close'   => '</article>',
 					'context' => 'entry-widget',
 				) );

 			}

 		}

		if ( isset( $instance['columns'] ) && ! empty( $instance['columns'] ) ) {
			echo '</div>';
		}

		$this->widget_end( $args );

 		// Restore original query.
 		wp_reset_query();

 		echo $this->cache_widget( $args, ob_get_clean() );

 		// The EXTRA Posts (list).
 		if ( ! empty( $instance['extra_num'] ) ) {
 			if ( ! empty( $instance['extra_title'] ) ) {
 				echo $args['before_title'] . '<span class="more-posts-title">' . esc_html( $instance['extra_title'] ) . '</span>' . $args['after_title'];
 			}

 			$offset = (int) $instance['product_num'] + (int) $instance['product_offset'];

 			$query_args = array(
 				'cat'       => $instance['product_cat'],
 				'showposts' => $instance['extra_num'],
 				'offset'    => $offset,
                 'tax_query' => array(
                     'relation' => 'AND',
                 ),
 			);

 			$wp_query = new WP_Query( $query_args );

 			$listitems = '';

 			if ( have_posts() ) {
 				while ( have_posts() ) {
 					the_post();
 					$listitems .= sprintf( '<li><a href="%s">%s</a></li>', get_permalink(), get_the_title() );
 				}

 				if ( mb_strlen( $listitems ) > 0 ) {
 					printf( '<ul class="more-posts">%s</ul>', $listitems );
 				}
 			}

 			// Restore original query.
 			wp_reset_query();
 		}

 		if ( ! empty( $instance['more_from_category'] ) && ! empty( $instance['product_cat'] ) ) {
 			printf(
 				'<p class="more-from-category"><a href="%1$s" title="%2$s">%3$s</a></p>',
 				esc_url( get_category_link( $instance['product_cat'] ) ),
 				esc_attr( get_cat_name( $instance['product_cat'] ) ),
 				esc_html( $instance['more_from_category_text'] )
 			);
 		}

 	}

 }

 add_action( 'widgets_init', 'genwooc_register_featured_products_widget' );
 function genwooc_register_featured_products_widget() {
 	register_widget( 'Genwooc_Featured_Products' );
 }
