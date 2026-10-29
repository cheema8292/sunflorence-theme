<?php
/**
 * Product info column — the Shopify product-info blocks in the same order and markup:
 * rating · title · price (SAVE %) · text · benefits grid · variant picker · add to cart · trust box · divider · info tabs.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product  = $args['product'];
$variable = $product->is_type( 'variable' );

// Prices of the product (variable: the default / first available variation, like Shopify's selected variant)
$start = $product;
$attrs = array();
$vars  = array();
if ( $variable ) {
	$vars = $product->get_available_variations();
	$defaults = $product->get_default_attributes();
	$pick     = null;
	foreach ( $vars as $v ) {
		$match = true;
		foreach ( $defaults as $k => $val ) {
			$vv = $v['attributes'][ 'attribute_' . $k ] ?? '';
			if ( '' !== $vv && $vv !== $val ) {
				$match = false;
				break;
			}
		}
		if ( $match && $v['is_in_stock'] && ( $defaults || ! $pick ) ) {
			$pick = $v;
			if ( $defaults ) {
				break;
			}
		}
	}
	if ( $pick ) {
		$start = wc_get_product( $pick['variation_id'] ) ?: $product;
		foreach ( $pick['attributes'] as $k => $val ) {
			$attrs[ substr( $k, 10 ) ] = $val; // strip "attribute_"
		}
	}
}
$price   = (float) wc_get_price_to_display( $start );
$regular = (float) wc_get_price_to_display( $start, array( 'price' => $start->get_regular_price() ) );
$on_sale = $regular > $price && $price > 0;
$save    = $on_sale ? (int) round( ( $regular - $price ) / $regular * 100 ) : 0;

// Rating
$rcount = (int) $product->get_review_count();
$avg    = (float) $product->get_average_rating();

$btn_text = sf_page_opt( 'product', 'btn_text' );
$intro    = sf_page_opt( 'product', 'intro_text' );
$benefits = sf_page_opt( 'product', 'blocks_benefit', array() );
if ( ! $benefits ) {
	$benefits = array(
		array( 'text' => 'Handpicked for quality and value' ),
		array( 'text' => 'Tracked shipping on every order' ),
		array( 'text' => 'Easy 30-day returns' ),
		array( 'text' => '100% secure checkout & flexible payment' ),
	);
}
$icon_bg  = sf_color( sf_page_opt( 'product', 'benefit_icon_bg' ), '#6e1f2f' );
$icon_col = sf_color( sf_page_opt( 'product', 'benefit_icon_color' ), '#e2d5c6' );
$sw       = 'fill="none" stroke="' . esc_attr( $icon_col ) . '" stroke-linecap="round" stroke-linejoin="round" stroke-width="12"';
$icons    = array(
	'<path d="M84.27,171.73l-55.09-20.3a7.92,7.92,0,0,1,0-14.86l55.09-20.3,20.3-55.09a7.92,7.92,0,0,1,14.86,0l20.3,55.09,55.09,20.3a7.92,7.92,0,0,1,0,14.86l-55.09,20.3-20.3,55.09a7.92,7.92,0,0,1-14.86,0Z" ' . $sw . '/><line x1="176" y1="16" x2="176" y2="64" ' . $sw . '/><line x1="224" y1="72" x2="224" y2="104" ' . $sw . '/><line x1="152" y1="40" x2="200" y2="40" ' . $sw . '/><line x1="208" y1="88" x2="240" y2="88" ' . $sw . '/>',
	'<circle cx="128" cy="128" r="96" ' . $sw . '/><polyline points="128 72 128 128 184 128" ' . $sw . '/>',
	'<line x1="80" y1="40" x2="80" y2="88" ' . $sw . '/><line x1="56" y1="64" x2="104" y2="64" ' . $sw . '/><line x1="168" y1="184" x2="168" y2="216" ' . $sw . '/><line x1="152" y1="200" x2="184" y2="200" ' . $sw . '/><line x1="144" y1="80" x2="176" y2="112" ' . $sw . '/><line x1="216" y1="128" x2="216" y2="176" ' . $sw . '/><line x1="192" y1="152" x2="240" y2="152" ' . $sw . '/><rect x="21.49" y="105.37" width="213.02" height="45.25" rx="8" transform="translate(-53.02 128) rotate(-45)" ' . $sw . '/>',
	'<path d="M54.46,201.54c-9.2-9.2-3.1-28.53-7.78-39.85C41.82,150,24,140.5,24,128s17.82-22,22.68-33.69C51.36,83,45.26,63.66,54.46,54.46S83,51.36,94.31,46.68C106.05,41.82,115.5,24,128,24S150,41.82,161.69,46.68c11.32,4.68,30.65-1.42,39.85,7.78s3.1,28.53,7.78,39.85C214.18,106.05,232,115.5,232,128S214.18,150,209.32,161.69c-4.68,11.32,1.42,30.65-7.78,39.85s-28.53,3.1-39.85,7.78C150,214.18,140.5,232,128,232s-22-17.82-33.69-22.68C83,204.64,63.66,210.74,54.46,201.54Z" ' . $sw . '/><polyline points="88 136 112 160 168 104" ' . $sw . '/>',
);

$trust = sf_page_opt( 'product', 'trust_text' );
if ( ! $trust ) {
	$links = array();
	$ret   = get_page_by_path( 'refund-policy' ) ?: get_page_by_path( 'returns' );
	$shp   = get_page_by_path( 'shipping-policy' ) ?: get_page_by_path( 'shipping' );
	$links[] = $ret ? '<a href="' . esc_url( get_permalink( $ret ) ) . '">30-Day Returns</a>' : '30-Day Returns';
	$links[] = $shp ? '<a href="' . esc_url( get_permalink( $shp ) ) . '">Tracked Shipping</a>' : 'Tracked Shipping';
	$trust   = '<p>' . implode( '</p><p>', $links ) . '</p>';
}

$tabs = array( array( 'DESCRIPTION', $product->get_description() ? apply_filters( 'the_content', $product->get_description() ) : '<p>' . esc_html__( 'See the product details above for everything you need to know about this item.', 'sunflorence' ) . '</p>' ) );
foreach ( array( 'shipping', 'returns', 'payment' ) as $t ) {
	$body = sf_page_opt( 'product', 'tab_' . $t );
	if ( $body ) {
		$tabs[] = array( sf_page_opt( 'product', 'tab_' . $t . '_title' ), $body );
	}
}
$compare_html = $on_sale ? wc_price( $regular ) : '';
?>
<?php if ( $rcount ) : ?>
	<a href="#sf-reviews" class="sf-pdp-rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %1$s out of 5, %2$d reviews', 'sunflorence' ), $avg, $rcount ) ); ?>">
		<span class="sf-pdp-rating__stars" style="--sf-rating: <?php echo esc_attr( $avg / 5 * 100 ); ?>%"></span>
		<span class="sf-pdp-rating__text"><?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?> (<?php echo esc_html( $rcount ); ?>)</span>
	</a>
<?php endif; ?>

<h1 class="shop-product-title product-title-title product_title entry-title" data-font-size="38" data-mobile-font-size="32"><?php echo esc_html( $product->get_name() ); ?></h1>

<div class="shop-product-price-container" id="block-price_sf" data-sf-price>
	<span class="shop-product-price-block<?php echo $on_sale ? ' on-sale' : ''; ?>" data-sf-price-current><?php echo $price > 0 ? wc_price( $price ) : wp_kses_post( $product->get_price_html() ); // phpcs:ignore ?></span>
	<span class="shop-compare-price" data-sf-price-compare<?php echo $on_sale ? '' : ' hidden'; ?>><?php echo $compare_html; // phpcs:ignore ?></span>
	<span class="shop-save-price" data-sf-price-save<?php echo $on_sale ? '' : ' hidden'; ?>>
		<svg class="tag-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor"><path d="M243.31,136,144,36.69A15.86,15.86,0,0,0,132.69,32H40a8,8,0,0,0-8,8v92.69A15.86,15.86,0,0,0,36.69,144L136,243.31a16,16,0,0,0,22.63,0l84.68-84.68a16,16,0,0,0,0-22.63ZM84,96A12,12,0,1,1,96,84,12,12,0,0,1,84,96Z"/></svg>
		<?php esc_html_e( 'SAVE', 'sunflorence' ); ?> <span data-sf-price-pct><?php echo esc_html( $save ); ?></span>%
	</span>
</div>

<?php if ( $product->get_short_description() ) : ?>
	<div class="custom-text-block-custom_text_VEHNzL sf-pdp-short">
		<div class="custom-text-wrapper"><div class="custom-text-content"><div class="custom-text-main"><div class="custom-text-main-text-content">
			<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
		</div></div></div></div>
	</div>
<?php endif; ?>

<?php if ( $intro ) : ?>
	<div class="custom-text-block-custom_text_VEHNzL">
		<div class="custom-text-wrapper"><div class="custom-text-content"><div class="custom-text-main"><div class="custom-text-main-text-content">
			<?php echo wp_kses_post( $intro ); ?>
		</div></div></div></div>
	</div>
<?php endif; ?>

<div class="bg-benefits-grid">
	<?php foreach ( array_slice( $benefits, 0, 4 ) as $i => $b ) : ?>
		<?php if ( ! sf_row( $b, 'text' ) ) { continue; } ?>
		<div class="bg-benefit-item">
			<div class="bg-benefit-icon" style="background-color: <?php echo esc_attr( $icon_bg ); ?>; color: <?php echo esc_attr( $icon_col ); ?>;">
				<svg width="24" height="24" viewBox="0 0 256 256" fill="none" xmlns="http://www.w3.org/2000/svg"><?php echo $icons[ $i % 4 ]; // phpcs:ignore ?></svg>
			</div>
			<div class="bg-benefit-text" style="color: #000000; font-size: 12px; font-weight: 600; letter-spacing: -0.3px; line-height: 1.4; font-family: var(--font-body-family-override);"><?php echo esc_html( sf_row( $b, 'text' ) ); ?></div>
		</div>
	<?php endforeach; ?>
</div>

<?php
if ( $variable || $product->is_type( 'simple' ) ) {
	get_template_part( 'template-parts/product/add-to-cart', null, array(
		'product'  => $product,
		'start'    => $start,
		'attrs'    => $attrs,
		'vars'     => $vars,
		'price'    => $price,
		'compare'  => $compare_html,
		'btn_text' => $btn_text,
	) );
} else {
	// grouped / external products: WooCommerce's own form
	echo '<div class="shop-add-to-cart-wrapper sf-wc-form">';
	woocommerce_template_single_add_to_cart();
	echo '</div>';
}
?>

<div class="custom-text-block-trust_badges_rr">
	<div class="custom-text-wrapper"><div class="custom-text-content"><div class="custom-text-main"><div class="custom-text-main-text-content">
		<?php echo wp_kses_post( $trust ); ?>
	</div></div></div></div>
</div>

<div class="product-divider-container" style="margin-top: 0px; margin-bottom: 10px; text-align: center; width: 100%; box-sizing: border-box; display: block; clear: both;">
	<hr class="product-divider-line" style="width: 100%; max-width: 100%; border: none; border-top: 1px solid #e4f9ff; margin: 0 auto; display: block; padding: 0; background: none; height: 0; box-sizing: border-box;">
</div>

<div class="product-faq" id="product-faq-faq">
	<div class="faq-container" data-accordion="true">
		<?php foreach ( $tabs as $tab ) : ?>
			<div class="faq-item">
				<div class="faq-question" role="button" tabindex="0" aria-expanded="false">
					<span class="faq-question-text"><?php echo esc_html( $tab[0] ); ?></span>
					<span class="faq-toggle">+</span>
				</div>
				<div class="faq-answer"><?php echo wp_kses_post( $tab[1] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php
$wl_page = get_page_by_path( 'wishlist' );
if ( $wl_page ) :
	$wl = array(
		'id'      => $product->get_id(),
		'title'   => $product->get_name(),
		'url'     => $product->get_permalink(),
		'image'   => (string) wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ),
		'price'   => $price > 0 ? html_entity_decode( wp_strip_all_tags( wc_price( $price ) ), ENT_QUOTES, 'UTF-8' ) : '',
		'compare' => $on_sale ? html_entity_decode( wp_strip_all_tags( wc_price( $regular ) ), ENT_QUOTES, 'UTF-8' ) : '',
		'type'    => sf_product_brand( $product ),
	);
	?>
	<button type="button" class="sf-wl-btn" data-sf-wishlist data-product="<?php echo esc_attr( wp_json_encode( $wl ) ); ?>" data-on="<?php esc_attr_e( 'Saved to wishlist', 'sunflorence' ); ?>" data-off="<?php esc_attr_e( 'Save to wishlist', 'sunflorence' ); ?>" aria-pressed="false">
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
		<span data-sf-wl-text><?php esc_html_e( 'Save to wishlist', 'sunflorence' ); ?></span>
	</button>
	<a class="sf-wl-link" href="<?php echo esc_url( get_permalink( $wl_page ) ); ?>"><?php esc_html_e( 'View wishlist', 'sunflorence' ); ?></a>
<?php endif; ?>
<?php
$meta = array();
if ( wc_product_sku_enabled() && $product->get_sku() ) {
	$meta[] = '<span class="sku_wrapper">' . esc_html__( 'SKU:', 'woocommerce' ) . ' <span class="sku">' . esc_html( $product->get_sku() ) . '</span></span>';
}
$cats = wc_get_product_category_list( $product->get_id(), ', ' );
if ( $cats ) {
	$meta[] = '<span class="posted_in">' . esc_html( _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) ) . ' ' . $cats . '</span>';
}
if ( $meta ) {
	echo '<div class="sf-pdp-meta product_meta">' . implode( '', $meta ) . '</div>'; // phpcs:ignore
}
