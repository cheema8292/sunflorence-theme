<?php
/**
 * The Shopify "shop-add-to-cart-button" (icon + "Add To Cart - $price ($compare)").
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$disabled = ! empty( $args['disabled'] );
?>
<button type="submit"<?php echo $args['name'] ? ' name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '"' : ''; ?>
	class="shop-add-to-cart-button product-form__submit single_add_to_cart_button button alt"
	style="--button-text-font-size: 12px; padding: 13px 20px; font-size: 12px; letter-spacing: var(--global-button-letter-spacing, 1px); font-weight: var(--global-button-font-weight, 700); background-color: #000000; color: #ffffff; --button-hover-color: #000000;"
	data-text="<?php echo esc_attr( $args['text'] ); ?>"
	<?php disabled( $disabled ); ?>>
	<span class="button-icon" style="color: #ffffff;">
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
	</span>
	<span class="button-text">
		<?php if ( $disabled ) : ?>
			<?php esc_html_e( 'Sold out', 'sunflorence' ); ?>
		<?php else : ?>
			<span data-sf-btn-label><?php echo esc_html( $args['text'] ); ?></span><?php if ( $args['price'] > 0 ) : ?> - <span data-sf-btn-price><?php echo wc_price( $args['price'] ); // phpcs:ignore ?></span><?php endif; ?>
			<span class="compare-price" data-sf-btn-compare<?php echo $args['compare'] ? '' : ' hidden'; ?>>(<?php echo $args['compare']; // phpcs:ignore ?>)</span>
		<?php endif; ?>
	</span>
	<div class="loading__spinner hidden">
		<svg aria-hidden="true" focusable="false" class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" cx="33" cy="33" r="30"></circle></svg>
	</div>
</button>
