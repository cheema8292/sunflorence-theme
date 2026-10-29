<?php
/**
 * Single product — same layout as the Shopify product page (templates/product.json):
 *   announcement bar → gallery + product info → customer reviews → "You May Also Like" → FAQ.
 * Texts are set in Sunflorence → Pages → Product page.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}
	$GLOBALS['product'] = $product;

	get_template_part( 'template-parts/product/announcement' );
	?>
	<div class="sf-notices"><?php do_action( 'woocommerce_before_single_product' ); ?></div>
	<?php if ( post_password_required() ) : ?>
		<div class="sf-container"><?php echo get_the_password_form(); // phpcs:ignore ?></div>
		<?php continue; ?>
	<?php endif; ?>

	<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'shop-product-section page-fade-in', $product ); ?> data-show-mobile-arrows="false">
		<div class="shop-product-inner-container">
			<div class="shop-product-layout">
				<?php get_template_part( 'template-parts/product/gallery', null, array( 'product' => $product ) ); ?>
				<div class="shop-product-info">
					<?php get_template_part( 'template-parts/product/info', null, array( 'product' => $product ) ); ?>
				</div>
			</div>
		</div>
	</div>

	<?php
	get_template_part( 'template-parts/product/reviews', null, array( 'product' => $product ) );

	// You May Also Like (Shopify rr-products section)
	$related = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), 8 ) ) );
	if ( ! $related ) {
		$related = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 8, 'exclude' => array( $product->get_id() ), 'orderby' => 'date', 'order' => 'DESC' ) );
	}
	get_template_part( 'template-parts/sections/featured-products', null, array(
		'products'  => $related,
		'heading'   => sf_page_opt( 'product', 'related_heading' ),
		'link_text' => sf_page_opt( 'product', 'related_link_text' ),
		'link_url'  => wc_get_page_permalink( 'shop' ),
	) );

	// FAQ (Shopify rr-home-faq section on the product page)
	$faqs = sf_page_opt( 'product', 'blocks_faq', array() );
	if ( ! $faqs ) {
		$faqs = array(
			array( 'question' => 'How long does shipping take?', 'answer' => '<p>Standard shipping typically takes 5-7 business days. Express shipping options are available at checkout.</p>' ),
			array( 'question' => 'Do you ship internationally?', 'answer' => '<p>Yes, we ship to over 50 countries worldwide. International shipping rates are calculated at checkout.</p>' ),
			array( 'question' => 'What is your return policy?', 'answer' => '<p>We offer a 30-day return policy on all items. Products must be in their original condition with tags attached.</p>' ),
			array( 'question' => 'What payment methods do you accept?', 'answer' => '<p>We accept all major credit cards, PayPal, Apple Pay, and Google Pay for your convenience.</p>' ),
			array( 'question' => 'How can I track my order?', 'answer' => "<p>Once your order ships, you'll receive a tracking number via email. You can use this to monitor your delivery status.</p>" ),
		);
	}
	get_template_part( 'template-parts/sections/faq-accordion', null, array(
		'faqs'    => $faqs,
		'heading' => sf_page_opt( 'product', 'faq_heading' ),
	) );

	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}
	do_action( 'woocommerce_after_single_product' );
endwhile;

get_footer();
