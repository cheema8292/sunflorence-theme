<?php
/**
 * WooCommerce integration.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

/** Cart item count (0 when WooCommerce is off). */
function sf_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/** Header cart badge — also returned as an AJAX fragment so it updates after add-to-cart. */
function sf_cart_badge_html() {
	$n = sf_cart_count();
	return '<span class="rr-nav__cart-count sf-cart-count"' . ( $n ? '' : ' hidden' ) . '>' . esc_html( $n ) . '</span>';
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['.sf-cart-count'] = sf_cart_badge_html();
	return $fragments;
} );

/**
 * Published, visible products in a product category (and its children).
 *
 * @return WC_Product[]
 */
function sf_products_in_cat( $term, $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$term = sf_term( $term );
	if ( ! $term ) {
		return array();
	}
	return wc_get_products( array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'category'   => array( $term->slug ),
		'limit'      => max( 1, (int) $limit ),
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	) );
}

/** Product category link with fallback to the shop page. */
function sf_cat_link( $term ) {
	$term = sf_term( $term );
	if ( $term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/** Sale percentage of a product (0 if not on sale). */
function sf_sale_percent( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}
	$regular = (float) ( $product->is_type( 'variable' ) ? $product->get_variation_regular_price( 'min' ) : $product->get_regular_price() );
	$sale    = (float) ( $product->is_type( 'variable' ) ? $product->get_variation_sale_price( 'min' ) : $product->get_sale_price() );
	return ( $regular > 0 && $sale > 0 && $sale < $regular ) ? (int) round( ( $regular - $sale ) / $regular * 100 ) : 0;
}

/** Product vendor/brand: "brand" attribute or the WooCommerce Brands taxonomy, if any. */
function sf_product_brand( $product ) {
	$brand = $product->get_attribute( 'brand' );
	if ( ! $brand && taxonomy_exists( 'product_brand' ) ) {
		$terms = get_the_terms( $product->get_id(), 'product_brand' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$brand = $terms[0]->name;
		}
	}
	return $brand;
}

/* ───────────── Cart drawer ───────────── */

/** Cart drawer contents (items + subtotal + buttons). Also an AJAX fragment, so it refreshes after every cart change. */
function sf_cart_drawer_content() {
	ob_start();
	$cart = WC()->cart;
	echo '<div class="sf-cd__content">';
	if ( ! $cart || $cart->is_empty() ) {
		$shop = wc_get_page_permalink( 'shop' );
		echo '<div class="sf-cd__empty"><p>' . esc_html__( 'Your cart is empty.', 'sunflorence' ) . '</p><a class="sf-cd__btn" href="' . esc_url( $shop ) . '">' . esc_html__( 'Continue shopping', 'sunflorence' ) . '</a></div>';
	} else {
		echo '<ul class="sf-cd__items">';
		foreach ( $cart->get_cart() as $key => $item ) {
			$p = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
			if ( ! $p || ! $p->exists() || $item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $item, $key ) ) {
				continue;
			}
			$link  = apply_filters( 'woocommerce_cart_item_permalink', $p->is_visible() ? $p->get_permalink( $item ) : '', $item, $key );
			$name  = apply_filters( 'woocommerce_cart_item_name', $p->get_name(), $item, $key );
			$thumb = apply_filters( 'woocommerce_cart_item_thumbnail', $p->get_image( 'woocommerce_thumbnail' ), $item, $key );
			$max   = $p->get_max_purchase_quantity();
			?>
			<li class="sf-cd__item" data-key="<?php echo esc_attr( $key ); ?>">
				<a class="sf-cd__img" href="<?php echo esc_url( $link ); ?>"><?php echo $thumb; // phpcs:ignore ?></a>
				<div class="sf-cd__info">
					<a class="sf-cd__name" href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $name ); ?></a>
					<div class="sf-cd__meta"><?php echo wc_get_formatted_cart_item_data( $item ); // phpcs:ignore ?></div>
					<div class="sf-cd__row">
						<div class="sf-cd__qty" data-max="<?php echo esc_attr( $max > 0 ? $max : '' ); ?>">
							<button type="button" data-sf-qty="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'sunflorence' ); ?>">&minus;</button>
							<span aria-live="polite"><?php echo esc_html( $item['quantity'] ); ?></span>
							<button type="button" data-sf-qty="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'sunflorence' ); ?>"<?php disabled( $max > 0 && $item['quantity'] >= $max ); ?>>+</button>
						</div>
						<span class="sf-cd__price"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', $cart->get_product_subtotal( $p, $item['quantity'] ), $item, $key ); // phpcs:ignore ?></span>
					</div>
				</div>
				<button type="button" class="sf-cd__remove" data-sf-remove aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s', 'sunflorence' ), wp_strip_all_tags( $name ) ) ); ?>">&times;</button>
			</li>
			<?php
		}
		echo '</ul>';
		?>
		<div class="sf-cd__foot">
			<div class="sf-cd__subtotal"><span><?php esc_html_e( 'Subtotal', 'sunflorence' ); ?></span><strong><?php echo $cart->get_cart_subtotal(); // phpcs:ignore ?></strong></div>
			<p class="sf-cd__note"><?php esc_html_e( 'Taxes and shipping calculated at checkout.', 'sunflorence' ); ?></p>
			<a class="sf-cd__btn sf-cd__btn--checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'sunflorence' ); ?></a>
			<a class="sf-cd__link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View cart', 'sunflorence' ); ?></a>
		</div>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['.sf-cd__content']     = sf_cart_drawer_content();
	$fragments['.sf-cd__count']       = '<span class="sf-cd__count">(' . esc_html( sf_cart_count() ) . ')</span>';
	return $fragments;
} );

/** The drawer itself — not on the cart/checkout pages. */
add_action( 'wp_footer', function () {
	if ( ! function_exists( 'WC' ) || is_cart() || is_checkout() ) {
		return;
	}
	?>
	<div class="sf-cd" id="sf-cart-drawer" hidden>
		<div class="sf-cd__overlay" data-sf-cart-close></div>
		<aside class="sf-cd__panel" role="dialog" aria-modal="true" aria-labelledby="sf-cd-title" tabindex="-1">
			<header class="sf-cd__head">
				<h2 id="sf-cd-title"><?php esc_html_e( 'Your Cart', 'sunflorence' ); ?> <span class="sf-cd__count">(<?php echo esc_html( sf_cart_count() ); ?>)</span></h2>
				<button type="button" class="sf-cd__close" data-sf-cart-close aria-label="<?php esc_attr_e( 'Close cart', 'sunflorence' ); ?>">&times;</button>
			</header>
			<?php echo sf_cart_drawer_content(); // phpcs:ignore ?>
		</aside>
	</div>
	<?php
} );

/** Change the quantity of a cart line (0 = remove) → returns the refreshed fragments. */
function sf_ajax_cart_qty() {
	check_ajax_referer( 'sf_cart', 'nonce' );
	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$qty = isset( $_POST['qty'] ) ? max( 0, (int) $_POST['qty'] ) : 0;
	if ( $key && WC()->cart->get_cart_item( $key ) ) {
		if ( 0 === $qty ) {
			WC()->cart->remove_cart_item( $key );
		} else {
			WC()->cart->set_quantity( $key, $qty, true );
		}
	}
	WC_AJAX::get_refreshed_fragments(); // sends JSON and exits
}
add_action( 'wp_ajax_sf_cart_qty', 'sf_ajax_cart_qty' );
add_action( 'wp_ajax_nopriv_sf_cart_qty', 'sf_ajax_cart_qty' );

/** Products per page on shop / category pages (Sunflorence → Pages → Category pages). */
add_filter( 'loop_shop_per_page', function ( $n ) {
	$v = (int) sf_page_opt( 'collection', 'products_per_page' );
	return $v > 0 ? $v : $n;
}, 20 );

/**
 * Products of the given brands, one brand after the other (round robin), for the brand strip.
 * Brand match: "Brands" taxonomy (product_brand), "Brand" attribute (pa_brand), else the brand name in the product name.
 * Not enough matches → filled up with the newest products.
 *
 * @return WC_Product[]
 */
function sf_brand_products( array $names, $limit = 4 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$base   = array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => $limit, 'stock_status' => 'instock', 'return' => 'ids' );
	$lists  = array();
	foreach ( array_filter( array_map( 'trim', $names ) ) as $name ) {
		$ids = array();
		foreach ( array( 'product_brand', 'pa_brand' ) as $tax ) {
			if ( ! $ids && taxonomy_exists( $tax ) ) {
				$term = get_term_by( 'name', $name, $tax );
				if ( $term ) {
					$ids = wc_get_products( $base + array( 'tax_query' => array( array( 'taxonomy' => $tax, 'terms' => $term->term_id ) ) ) ); // phpcs:ignore
				}
			}
		}
		if ( ! $ids ) {
			$ids = wc_get_products( $base + array( 's' => $name ) );
		}
		if ( $ids ) {
			$lists[] = $ids;
		}
	}
	$picked = array();
	for ( $round = 0; count( $picked ) < $limit && $lists && $round < $limit; $round++ ) {
		foreach ( $lists as $ids ) {
			if ( isset( $ids[ $round ] ) && ! in_array( $ids[ $round ], $picked, true ) ) {
				$picked[] = $ids[ $round ];
				if ( count( $picked ) >= $limit ) {
					break;
				}
			}
		}
	}
	if ( count( $picked ) < $limit ) {
		$more   = wc_get_products( array_merge( $base, array( 'limit' => $limit, 'exclude' => $picked ?: array( 0 ), 'orderby' => 'date', 'order' => 'DESC' ) ) );
		$picked = array_merge( $picked, array_slice( $more, 0, $limit - count( $picked ) ) );
	}
	return array_filter( array_map( 'wc_get_product', $picked ) );
}
