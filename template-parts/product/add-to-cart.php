<?php
/**
 * Variant picker + add to cart (Shopify snippets/simple-variant-picker.liquid + the add_to_cart block).
 *
 * Variable products keep WooCommerce's standard variation form (hidden selects + variation_id), so
 * stock, prices and "any" attributes work as usual — the Shopify-style buttons/swatches just drive the selects.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product  = $args['product'];
$start    = $args['start'];
$variable = $product->is_type( 'variable' );
$in_stock = $variable ? (bool) $args['vars'] : $product->is_in_stock() && $product->is_purchasable();
$btn_text = $args['btn_text'];

// colour names → hex, when the attribute term has no colour saved (term meta "color")
$named = array( 'black' => '#000000', 'white' => '#FFFFFF', 'red' => '#FF0000', 'blue' => '#1E4FD8', 'navy' => '#1F2A44', 'green' => '#2E7D32', 'yellow' => '#F5D000', 'pink' => '#F4A6C0', 'purple' => '#7B3FA0', 'orange' => '#F28C28', 'brown' => '#7B4A2D', 'beige' => '#E8D9C0', 'nude' => '#E3BC9A', 'grey' => '#9E9E9E', 'gray' => '#9E9E9E', 'gold' => '#C9A43B', 'silver' => '#C0C0C0', 'rose' => '#E8A0A8', 'khaki' => '#B5A06A', 'cream' => '#F3E9D2', 'burgundy' => '#6E1F2F', 'wine' => '#6E1F2F', 'coffee' => '#6F4E37', 'apricot' => '#F7C59F', 'sky blue' => '#87CEEB', 'light blue' => '#ADD8E6', 'dark blue' => '#1F2A44', 'army green' => '#4B5320', 'dark green' => '#1F4D2B', 'light grey' => '#D3D3D3', 'dark grey' => '#555555', 'champagne' => '#F1DDCF', 'transparent' => '#F7F7F7', 'clear' => '#F7F7F7' );

$form_action = apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() );
?>
<?php if ( $variable ) :
	$attributes = $product->get_variation_attributes();
	$vjson      = wp_json_encode( $args['vars'] );
	?>
	<form class="variations_form cart sf-pform" action="<?php echo esc_url( $form_action ); ?>" method="post" enctype="multipart/form-data"
		data-product_id="<?php echo absint( $product->get_id() ); ?>"
		data-product_variations="<?php echo function_exists( 'wc_esc_json' ) ? wc_esc_json( $vjson ) : _wp_specialchars( $vjson, ENT_QUOTES, 'UTF-8', true ); // phpcs:ignore ?>">

		<div class="simple-variant-picker" style="--label-color: #000000; --label-size: 14px; --show-labels: block; --option-bg-color: #FFFFFF; --option-border-color: #DDDDDD; --option-text-color: #000000; --option-size: 14px; --option-border-radius: 4px; --option-padding-vertical: 8px; --option-padding-horizontal: 15px; --option-font-size: 14px; --option-image-size: 60px; --container-margin-left: 5px; --container-margin-right: 0px; --selected-bg-color: #000000; --selected-text-color: #FFFFFF; --selected-border-color: #000000; --swatch-size: 30px; --swatch-bg-color: #CCCCCC; --outline-color: #e5e5e5; --image-variant-size: 60px; --variant-image-border-width: 1px; --variant-image-border-color: #e5e5e5; --variant-image-border-radius: 4px; --variant-image-selected-border-color: #007acc; --variant-image-selected-border-width: 2px; --show-variant-shadows: 1; margin-top: 0px;">
			<div class="simple-variant-picker__container">
				<?php
				$n = 0;
				foreach ( $attributes as $attr_name => $options ) :
					$n++;
					$slug     = sanitize_title( $attr_name );
					$label    = wc_attribute_label( $attr_name, $product );
					$is_color = (bool) preg_match( '/colou?r|couleur|shade/i', $label );
					$is_size  = ! $is_color && (bool) preg_match( '/size|taille/i', $label );
					$selected = $args['attrs'][ $slug ] ?? '';
					// values in the product's order, with display names
					$values = array();
					if ( taxonomy_exists( $attr_name ) ) {
						foreach ( wc_get_product_terms( $product->get_id(), $attr_name, array( 'fields' => 'all' ) ) as $term ) {
							if ( in_array( $term->slug, $options, true ) ) {
								$values[] = array( $term->slug, $term->name, $term );
							}
						}
					} else {
						foreach ( $options as $o ) {
							$values[] = array( $o, $o, null );
						}
					}
					// Shopify shows sizes as small circles (S, M, XL …) — longer values ("100 ml") use normal buttons
					if ( $is_size ) {
						foreach ( $values as $v ) {
							if ( mb_strlen( $v[1] ) > 3 ) {
								$is_size = false;
								break;
							}
						}
					}
					if ( '' === $selected && $values ) {
						$selected = $values[0][0];
					}
					$sel_name = '';
					foreach ( $values as $v ) {
						if ( $v[0] === $selected ) {
							$sel_name = $v[1];
						}
					}
					?>
					<div class="simple-variant-picker__option option--<?php echo $is_color ? 'color' : ( $is_size ? 'size' : 'default' ); ?>" data-sf-attr="attribute_<?php echo esc_attr( $slug ); ?>">
						<div class="simple-variant-picker__label-row">
							<label class="simple-variant-picker__label" id="sf-opt-<?php echo esc_attr( $n ); ?>-label"><?php echo esc_html( $label ); ?>:</label>
							<div class="simple-variant-picker__selected-container">
								<span class="simple-variant-picker__selected-variant" style="color: #000000;" data-sf-selected><?php echo esc_html( $sel_name ); ?></span>
							</div>
						</div>
						<div class="simple-variant-picker__values" role="radiogroup" aria-labelledby="sf-opt-<?php echo esc_attr( $n ); ?>-label">
							<?php foreach ( $values as $i => $v ) :
								$id = 'sf-opt-' . $n . '-' . $i;
								?>
								<div class="simple-variant-picker__value-container value--<?php echo $is_color ? 'color' : ( $is_size ? 'size' : 'default' ); ?>">
									<input type="radio" id="<?php echo esc_attr( $id ); ?>" name="sf_<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $v[0] ); ?>" data-name="<?php echo esc_attr( $v[1] ); ?>" class="simple-variant-picker__radio" <?php checked( $v[0], $selected ); ?>>
									<?php if ( $is_color ) :
										$hex = $v[2] ? sanitize_hex_color( (string) get_term_meta( $v[2]->term_id, 'color', true ) ) : '';
										$hex = $hex ?: ( $named[ strtolower( trim( $v[1] ) ) ] ?? '' );
										?>
										<?php if ( $hex ) : ?>
											<label for="<?php echo esc_attr( $id ); ?>" class="simple-variant-picker__color-swatch" title="<?php echo esc_attr( $v[1] ); ?>" style="background-color: <?php echo esc_attr( $hex ); ?>; width: var(--swatch-size); height: var(--swatch-size);"><span class="visually-hidden screen-reader-text"><?php echo esc_html( $v[1] ); ?></span></label>
										<?php else : // unknown colour name → text button ?>
											<label for="<?php echo esc_attr( $id ); ?>" class="simple-variant-picker__value"><span><?php echo esc_html( $v[1] ); ?></span></label>
										<?php endif; ?>
									<?php else : ?>
										<label for="<?php echo esc_attr( $id ); ?>" class="simple-variant-picker__value<?php echo $is_size ? ' size-value' : ''; ?>"><span><?php echo esc_html( $v[1] ); ?></span></label>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php // WooCommerce's selects (hidden) — read by wc-add-to-cart-variation.js ?>
		<table class="variations sf-variations-hidden" role="presentation" hidden>
			<tbody>
				<?php foreach ( $attributes as $attr_name => $options ) : ?>
					<tr><td class="value">
						<?php
						wc_dropdown_variation_attribute_options( array(
							'options'   => $options,
							'attribute' => $attr_name,
							'product'   => $product,
							'selected'  => $args['attrs'][ sanitize_title( $attr_name ) ] ?? false,
						) );
						?>
					</td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<a class="reset_variations" href="#" hidden><?php esc_html_e( 'Clear', 'woocommerce' ); ?></a>

		<div class="single_variation_wrap">
			<div class="woocommerce-variation single_variation" hidden></div>
			<div class="woocommerce-variation-add-to-cart variations_button shop-add-to-cart-wrapper">
				<input type="hidden" name="quantity" value="1">
				<?php
				get_template_part( 'template-parts/product/atc-button', null, array(
					'name'     => '',
					'value'    => '',
					'text'     => $btn_text,
					'price'    => $args['price'],
					'compare'  => $args['compare'],
					'disabled' => ! $in_stock,
				) );
				?>
				<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
				<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
				<input type="hidden" name="variation_id" class="variation_id" value="0">
			</div>
		</div>
		<p class="sf-atc-msg" role="status" aria-live="polite" hidden></p>
	</form>

<?php else : ?>
	<form class="cart sf-pform" action="<?php echo esc_url( $form_action ); ?>" method="post" enctype="multipart/form-data">
		<div class="shop-add-to-cart-wrapper">
			<input type="hidden" name="quantity" value="1">
			<?php
			get_template_part( 'template-parts/product/atc-button', null, array(
				'name'     => 'add-to-cart',
				'value'    => $product->get_id(),
				'text'     => $btn_text,
				'price'    => $args['price'],
				'compare'  => $args['compare'],
				'disabled' => ! $in_stock,
			) );
			?>
		</div>
		<p class="sf-atc-msg" role="status" aria-live="polite" hidden></p>
	</form>
<?php endif; ?>
