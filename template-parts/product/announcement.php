<?php
/**
 * Product page announcement bar (Shopify custom-announcement-bar).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$text = sf_page_opt( 'product', 'announce_text' );
if ( ! $text ) {
	return;
}
$style = sprintf(
	'background:%s;color:%s;',
	sf_color( sf_page_opt( 'product', 'announce_bg' ), '#000000' ),
	sf_color( sf_page_opt( 'product', 'announce_color' ), '#ffffff' )
);
?>
<div class="announcement-bar-section announcement-bar-section-product" style="<?php echo esc_attr( $style ); ?>">
	<p><?php echo esc_html( $text ); ?></p>
</div>
